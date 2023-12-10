<?php

namespace App\Service;

use App\Entity\Program;
use App\Entity\Season;

class ProgramDuration
{
    public function calculate(Program $program): int
    {
        $programDuration = 0;

        $seasons = $program->getSeason();

        foreach ($seasons as $season) {
            $programDuration = $this->calculateAll($season);
        }
        return $programDuration;
    }

    public function calculateAll(Season $season): int
    {
        $programDuration = 0;

        $episodes = $season->getEpisodes();

        foreach ($episodes as $episode) {
            $programDuration = $episode->getDuration();
        }
        return $programDuration;
    }
}
