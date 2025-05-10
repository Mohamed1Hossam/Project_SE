<?php
class HomeModel {
    private $stats;

    public function __construct() {
        // In a real application, these would come from a database
        $this->stats = [
            'totalDonations' => '2.5M+',
            'peopleHelped' => '10,000+',
            'activeDonors' => '5,000+'
        ];
    }

    public function getStats() {
        return $this->stats;
    }

    public function getFeatures() {
        return [
            [
                'icon' => 'calculator',
                'title' => 'Calculate Zakat',
                'description' => 'Use our Zakat calculator to determine your religious obligation accurately.'
            ],
            [
                'icon' => 'landmark',
                'title' => 'Financial Aid',
                'description' => 'Request assistance or contribute to those in need of financial support.'
            ],
            [
                'icon' => 'users',
                'title' => 'Join Community',
                'description' => 'Connect with others and be part of our growing charitable community.'
            ]
        ];
    }
}
