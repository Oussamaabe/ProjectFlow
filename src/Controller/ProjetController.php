<?php
// src/Controller/ProjetController.php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetManagementType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projets')]
class ProjetController extends AbstractController
{
    #[Route('/', name: 'app_projet_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ProjetRepository $repository): Response
    {
        $projetsEnCours = $repository->findBy(['isCompleted' => false]);
        $projetsAcheves = $repository->findBy(['isCompleted' => true]);

        return $this->render('projet/index.html.twig', [
            'projetsEnCours' => $projetsEnCours,
            'projetsAcheves' => $projetsAcheves,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Projet $projet): Response
    {
        $formattedData = $this->formatProjectData($projet);

        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
            'formatted' => $formattedData,
            'totalPrice' => $projet->calculateTotalPrice(),
            'totalInvoiced' => $projet->calculateTotalInvoiced()
        ]);
    }

    #[Route('/{id}/manage', name: 'app_projet_manage', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
public function manage(Request $request, Projet $projet, EntityManagerInterface $em): Response
{
    // Initialiser les données si nécessaire
    if (null === $projet->getDeadlines()) {
        $projet->setDeadlines([
            ['name' => 'Lancement', 'planned' => null, 'actual' => null, 'status' => 'planned'],
            ['name' => 'Phase intermédiaire', 'planned' => null, 'actual' => null, 'status' => 'planned'],
            ['name' => 'Livraison', 'planned' => null, 'actual' => null, 'status' => 'planned']
        ]);
    }

    $form = $this->createForm(ProjetManagementType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Traiter les données de date pour les deadlines
        $deadlines = $projet->getDeadlines() ?? [];
        $processedDeadlines = [];
        
        foreach ($deadlines as $deadline) {
            // Convertir les chaînes en objets DateTime si nécessaire
            if (is_string($deadline['planned'])) {
                $deadline['planned'] = \DateTime::createFromFormat('Y-m-d', $deadline['planned']);
            }
            
            if (is_string($deadline['actual'])) {
                $deadline['actual'] = \DateTime::createFromFormat('Y-m-d', $deadline['actual']);
            }
            
            $processedDeadlines[] = $deadline;
        }
        
        $projet->setDeadlines($processedDeadlines);
        
        // Mise à jour des totaux pour le bordereau des prix
        $this->updatePriceListTotals($projet);

        $em->flush();
        $this->addFlash('success', 'Projet mis à jour avec succès');
        return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
    }

    return $this->render('projet/manage.html.twig', [
        'projet' => $projet,
        'form' => $form->createView(),
        'totalPrice' => $projet->calculateTotalPrice(),
        'totalInvoiced' => $projet->calculateTotalInvoiced(),
        'active_tab' => $request->query->get('tab', 'deadlines')
    ]);
}

    #[Route('/{id}/complete', name: 'app_projet_complete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function complete(Request $request, Projet $projet, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('complete' . $projet->getId(), $request->request->get('_token'))) {
            $projet->setIsCompleted(true);
            $projet->setDateFinPrevue(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'Projet marqué comme achevé');
        }

        return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
    }

    #[Route('/dashboard', name: 'app_projet_statistiques')]
    #[IsGranted('ROLE_ADMIN')]
    public function statistiques(ProjetRepository $projetRepository): Response
    {
        // Statistiques basiques
        $totalProjets = $projetRepository->count([]);
        $projetsTermines = $projetRepository->count(['isCompleted' => true]);
        $projetsEnCours = $totalProjets - $projetsTermines;

        // Répartition par statut
        $statusDistribution = $projetRepository->getStatusDistribution();

        // Évolution mensuelle
        $monthlyData = $projetRepository->getMonthlyProjectEvolution();
        $monthlyLabels = [];
        $monthlyCounts = [];

        foreach ($monthlyData as $data) {
            $monthlyLabels[] = $data['month'];
            $monthlyCounts[] = $data['count'];
        }

        // Délai moyen de réalisation
        $averageCompletionTime = $projetRepository->getAverageCompletionTime();

        // Top 5 projets par budget
        $topBudgetProjects = $projetRepository->findTopBudgetProjects(5);

        return $this->render('dashboard/index.html.twig', [
            'totalProjets' => $totalProjets,
            'projetsTermines' => $projetsTermines,
            'projetsEnCours' => $projetsEnCours,
            'statusDistribution' => $statusDistribution,
            'monthlyLabels' => $monthlyLabels,
            'monthlyCounts' => $monthlyCounts,
            'averageCompletionTime' => $averageCompletionTime,
            'topBudgetProjects' => $topBudgetProjects
        ]);
    }
    /**
     * Formate les données du projet pour l'affichage
     */
    private function formatProjectData(Projet $projet): array
    {
        $formatted = [
            'deadlines' => [],
            'priceList' => [],
            'invoices' => [],
            'documents' => []
        ];

        // Formatter les deadlines
        $deadlines = $projet->getDeadlines() ?? [];
        foreach ($deadlines as $deadline) {
            $planned = $this->normalizeDateForDisplay($deadline['planned'] ?? null);
            $actual = $this->normalizeDateForDisplay($deadline['actual'] ?? null);

            $formattedDeadline = [
                'name' => $deadline['name'] ?? 'Non défini',
                'planned' => $planned ? $planned->format('d/m/Y') : '-',
                'actual' => $actual ? $actual->format('d/m/Y') : '-',
                'status' => $deadline['status'] ?? 'Non défini'
            ];

            $formatted['deadlines'][] = $formattedDeadline;
        }

        // Formatter le bordereau des prix
        $priceList = $projet->getPriceList() ?? [];
        foreach ($priceList as $item) {
            $formattedItem = [
                'description' => $item['description'] ?? 'Non défini',
                'quantity' => $item['quantity'] ?? 0,
                'unit' => $item['unit'] ?? '-',
                'unitPrice' => isset($item['unitPrice']) ? number_format($item['unitPrice'], 2) : '0.00',
                'total' => isset($item['total']) ? number_format($item['total'], 2) : '0.00'
            ];

            $formatted['priceList'][] = $formattedItem;
        }

        // Formatter les factures
        $invoices = $projet->getInvoices() ?? [];
        foreach ($invoices as $invoice) {
            $formattedInvoice = [
                'reference' => $invoice['reference'] ?? 'Non défini',
                'amount' => isset($invoice['amount']) ? number_format($invoice['amount'], 2) : '0.00',
                'status' => $invoice['status'] ?? 'Non défini'
            ];

            $formatted['invoices'][] = $formattedInvoice;
        }

        // Formatter les documents
        $documents = $projet->getDocuments() ?? [];
        foreach ($documents as $document) {
            $formattedDocument = [
                'name' => $document['name'] ?? 'Document sans nom',
                'type' => $document['type'] ?? 'Autre',
                'date' => isset($document['date'])
                    ? (new \DateTime($document['date']))->format('d/m/Y')
                    : '-'
            ];

            $formatted['documents'][] = $formattedDocument;
        }

        return $formatted;
    }

    /**
     * Normalise les formats de date dans les deadlines
     */
    private function normalizeDeadlineDates(array $deadlines): array
    {
        foreach ($deadlines as &$deadline) {
            if (isset($deadline['planned'])) {
                $deadline['planned'] = $this->normalizeDateValue($deadline['planned']);
            }

            if (isset($deadline['actual'])) {
                $deadline['actual'] = $this->normalizeDateValue($deadline['actual']);
            }
        }

        return $deadlines;
    }

    /**
     * Normalise une valeur de date (peut être un DateTime, un tableau ou une string)
     */
    private function normalizeDateValue($date)
    {
        if ($date instanceof \DateTimeInterface) {
            return $date;
        }

        if (is_array($date) && isset($date['date'])) {
            try {
                return \DateTime::createFromFormat('Y-m-d', $date['date']);
            } catch (\Exception $e) {
                return null;
            }
        }

        if (is_string($date)) {
            try {
                return \DateTime::createFromFormat('Y-m-d', $date);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Normalise différents formats de date pour l'affichage
     */
    private function normalizeDateForDisplay($date): ?\DateTimeInterface
    {
        if ($date instanceof \DateTimeInterface) {
            return $date;
        }

        if (is_array($date) && isset($date['date'])) {
            return new \DateTime($date['date']);
        }

        if (is_string($date)) {
            return \DateTime::createFromFormat('Y-m-d', $date);
        }

        return null;
    }

    /**
     * Met à jour les totaux dans le bordereau des prix
     */
    private function updatePriceListTotals(Projet $projet): void
    {
        $priceList = $projet->getPriceList() ?? [];
        $updatedPriceList = [];

        foreach ($priceList as $item) {
            if (isset($item['quantity'], $item['unitPrice'])) {
                $item['total'] = $item['quantity'] * $item['unitPrice'];
            }
            $updatedPriceList[] = $item;
        }

        $projet->setPriceList($updatedPriceList);
    }
}
