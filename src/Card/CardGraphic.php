<?php

namespace App\Card;

class CardGraphic extends Card
{ 
    /**
    * @var array<string, string>
    */
    private array $suitSymbols = [
        'Hearts' => 'hearts',
        'Diamonds' => 'diamonds',
        'Clubs' => 'clubs',
        'Spades' => 'spades'
    ];

    public function getAsString(): string
    {
        $suit = $this->suitSymbols[$this->suit] ?? $this->suit;
        return "{$this->value} {$suit}";
    }

    public function getSvgUrl(): string
    {
        $suit = $this->suitSymbols[$this->suit] ?? $this->suit;
        $value = strtolower($this->value);
        if ($value === 'a') {
            $value = 'ace';
        } elseif ($value === 'j') {
            $value = 'jack';
        } elseif ($value === 'q') {
            $value = 'queen';
        } elseif ($value === 'k') {
            $value = 'king';
        }
        return "https://upload.wikimedia.org/wikipedia/commons/" . $this->getSvgFilePath($value, $suit);
    }

    private function getSvgFilePath(string $value, string $suit): string
    {
        $paths = [
            'ace_of_clubs' => '5/5f/English_pattern_ace_of_clubs.svg',
            '2_of_clubs' => '3/30/English_pattern_2_of_clubs.svg',
            '3_of_clubs' => '1/14/English_pattern_3_of_clubs.svg',
            '4_of_clubs' => 'c/c0/English_pattern_4_of_clubs.svg',
            '5_of_clubs' => '7/74/English_pattern_5_of_clubs.svg',
            '6_of_clubs' => '0/02/English_pattern_6_of_clubs.svg',
            '7_of_clubs' => '6/60/English_pattern_7_of_clubs.svg',
            '8_of_clubs' => 'f/f0/English_pattern_8_of_clubs.svg',
            '9_of_clubs' => '1/14/English_pattern_9_of_clubs.svg',
            '10_of_clubs' => '4/48/English_pattern_10_of_clubs.svg',
            'jack_of_clubs' => '8/80/English_pattern_jack_of_clubs.svg',
            'queen_of_clubs' => 'b/b3/English_pattern_queen_of_clubs.svg',
            'king_of_clubs' => '3/3e/English_pattern_king_of_clubs.svg',
            'ace_of_diamonds' => '0/00/English_pattern_ace_of_diamonds.svg',
            '2_of_diamonds' => '9/99/English_pattern_2_of_diamonds.svg',
            '3_of_diamonds' => '2/2c/English_pattern_3_of_diamonds.svg',
            '4_of_diamonds' => '4/4e/English_pattern_4_of_diamonds.svg',
            '5_of_diamonds' => '6/6c/English_pattern_5_of_diamonds.svg',
            '6_of_diamonds' => '4/4e/English_pattern_6_of_diamonds.svg',
            '7_of_diamonds' => '5/5d/English_pattern_7_of_diamonds.svg',
            '8_of_diamonds' => '1/18/English_pattern_8_of_diamonds.svg',
            '9_of_diamonds' => 'f/f5/English_pattern_9_of_diamonds.svg',
            '10_of_diamonds' => 'd/da/English_pattern_10_of_diamonds.svg',
            'jack_of_diamonds' => '1/16/English_pattern_jack_of_diamonds.svg',
            'queen_of_diamonds' => '4/4f/English_pattern_queen_of_diamonds.svg',
            'king_of_diamonds' => '1/1c/English_pattern_king_of_diamonds.svg',
            'ace_of_hearts' => 'd/d4/English_pattern_ace_of_hearts.svg',
            '2_of_hearts' => '2/26/English_pattern_2_of_hearts.svg',
            '3_of_hearts' => '0/0f/English_pattern_3_of_hearts.svg',
            '4_of_hearts' => 'b/bb/English_pattern_4_of_hearts.svg',
            '5_of_hearts' => 'c/c6/English_pattern_5_of_hearts.svg',
            '6_of_hearts' => 'd/da/English_pattern_6_of_hearts.svg',
            '7_of_hearts' => 'c/cb/English_pattern_7_of_hearts.svg',
            '8_of_hearts' => '3/3c/English_pattern_8_of_hearts.svg',
            '9_of_hearts' => '2/22/English_pattern_9_of_hearts.svg',
            '10_of_hearts' => 'b/bb/English_pattern_10_of_hearts.svg',
            'jack_of_hearts' => '5/56/English_pattern_jack_of_hearts.svg',
            'queen_of_hearts' => '9/9d/English_pattern_queen_of_hearts.svg',
            'king_of_hearts' => '1/14/English_pattern_king_of_hearts.svg',
            'ace_of_spades' => '1/19/English_pattern_ace_of_spades.svg',
            '2_of_spades' => '0/0b/English_pattern_2_of_spades.svg',
            '3_of_spades' => 'a/a5/English_pattern_3_of_spades.svg',
            '4_of_spades' => '3/34/English_pattern_4_of_spades.svg',
            '5_of_spades' => '9/9c/English_pattern_5_of_spades.svg',
            '6_of_spades' => 'a/ac/English_pattern_6_of_spades.svg',
            '7_of_spades' => 'd/d1/English_pattern_7_of_spades.svg',
            '8_of_spades' => '4/4d/English_pattern_8_of_spades.svg',
            '9_of_spades' => 'f/f0/English_pattern_9_of_spades.svg',
            '10_of_spades' => 'd/da/English_pattern_10_of_spades.svg',
            'jack_of_spades' => '4/4f/English_pattern_jack_of_spades.svg',
            'queen_of_spades' => 'c/ca/English_pattern_queen_of_spades.svg',
            'king_of_spades' => 'f/f1/English_pattern_king_of_spades.svg',
        ];
        return $paths["{$value}_of_{$suit}"] ?? '';
    }
}
