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
            $seasonDuration = $this->calculateAll($season);
            $programDuration += $seasonDuration;
        }
        return $programDuration;
    }

    public function calculateAll(Season $season): int
    {
        $seasonDuration = 0;

        $episodes = $season->getEpisodes();

        foreach ($episodes as $episode) {
            $seasonDuration += $episode->getDuration();
        }
        return $seasonDuration;
    }
}
