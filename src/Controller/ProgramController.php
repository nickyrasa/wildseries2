<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Service\ProgramDuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    #[ROUTE('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);

            $entityManager->persist($program);
            $entityManager->flush();

            $this->addFlash('success', 'The new program has been created');

            return $this->redirectToRoute('program_index');
        }
        return $this->render('program/new.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/{slug}', name: 'show')]
    public function show(Program $program, ProgramDuration $programDuration): Response
    {
        $programDuration = $programDuration->calculate($program);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'slug' => $program->getSlug(),
            'programDuration' => $programDuration,

        ]);
    }

    #[Route('/{slug}/season/{season}', name: 'season_show')]
    public function showSeason(Program $program, Season $season, ProgramDuration $programDuration): Response
    {
        $programDuration = $programDuration->calculate($program);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'slug' => $program->getSlug(),
            'season' => $season,
            'programDuration' => $programDuration,
        ]);
    }

    #[Route('/{slug}/season/{seasonId}/episode/{episodeId}', name: 'episode_show')]
    public function showEpisode(
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,
        #[MapEntity(mapping: ['seasonId' => 'id'])] Season $season,
        #[MapEntity(mapping: ['episodeId' => 'id'])] Episode $episode,ProgramDuration $programDuration

    ) {
        $programDuration = $programDuration->calculate($program);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'slug' => $program->getSlug(),
            'season' => $season,
            'episode' => $episode,
            'programDuration' => $programDuration,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
            $this->addFlash('danger', 'The program has been deleted');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
}
