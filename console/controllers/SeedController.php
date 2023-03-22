<?php

namespace console\controllers;

use Carbon\Carbon;
use common\models\Trivia;
use common\models\TriviaAnswer;
use common\models\TriviaQuestion;
use Ramsey\Uuid\Uuid;
use yii\console\Controller;

class SeedController extends Controller
{


    public function actionTrivia()
    {
        $now      = Carbon::now()->format('Y-m-d H:i:s');
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d H:i:s');


        $triviaSeeds = [
            ['Trivia 1', -7.935214099999999, 112.62515099999999, 0, 10, 50, $now, $tomorrow],
            ['Trivia 2', -7.935214099999999, 112.62515099999999, 0, 10, 50, $now, $tomorrow],
            ['Trivia 3', -7.935214099999999, 112.62515099999999, 0, 10, 50, $now, $tomorrow],
            ['Trivia 4', -7.935214099999999, 112.62515099999999, 0, 10, 50, $now, $tomorrow],
            ['Trivia 5', -7.935214099999999, 112.62515099999999, 0, 10, 50, $now, $tomorrow],
        ];

        foreach ($triviaSeeds as $triviaSeed) {
            $trivia                = new Trivia();
            $trivia->name          = $triviaSeed[0];
            $trivia->latitude      = $triviaSeed[1];
            $trivia->longitude     = $triviaSeed[2];
            $trivia->altitude      = $triviaSeed[3];
            $trivia->scoreFound    = $triviaSeed[4];
            $trivia->scoreBonus    = $triviaSeed[5];
            $trivia->appearedAt    = $triviaSeed[6];
            $trivia->disappearedAt = $triviaSeed[7];

            $trivia->save();

            for ($iC = 1; $iC <= 5; $iC++) {
                $triviaQuestion           = new TriviaQuestion();
                $triviaQuestion->triviaId = $trivia->id;
                $triviaQuestion->question = $trivia->name . " Soal ke-$iC";
                $triviaQuestion->save();

                for ($iD = 1; $iD <= 4; $iD++) {
                    $triviaAnswer                   = new TriviaAnswer();
                    $triviaAnswer->triviaQuestionId = $triviaQuestion->id;
                    $triviaAnswer->answer           = $trivia->name . " Soal ke-$iC Jawaban $iD";
                    $triviaAnswer->score            = $iD;
                    $triviaAnswer->save();
                }
            }
        }
    }
}