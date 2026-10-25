<?php

namespace App\Services;

class GeoDistanceService
{
    /**
     * Approximate Centroid Coordinates (Lat, Lon) for 64 Districts of Bangladesh.
     */
    public static array $districtCoordinates = [
        'Dhaka' => [23.8103, 90.4125],
        'Gazipur' => [23.9999, 90.4203],
        'Narayanganj' => [23.6238, 90.5000],
        'Tangail' => [24.2513, 89.9167],
        'Narsingdi' => [23.9193, 90.7176],
        'Manikganj' => [23.8644, 90.0047],
        'Munshiganj' => [23.5422, 90.5305],
        'Faridpur' => [23.6071, 89.8429],
        'Gopalganj' => [23.0051, 89.8266],
        'Madaripur' => [23.1641, 90.1897],
        'Rajbari' => [23.7574, 89.6445],
        'Shariatpur' => [23.2423, 90.4348],
        'Kishoreganj' => [24.4449, 90.7766],

        'Chattogram' => [22.3569, 91.7832],
        'Chittagong' => [22.3569, 91.7832],
        'Cox\'s Bazar' => [21.4272, 92.0058],
        'Coxs Bazar' => [21.4272, 92.0058],
        'Cumilla' => [23.4607, 91.1809],
        'Comilla' => [23.4607, 91.1809],
        'Feni' => [23.0186, 91.3966],
        'Brahmanbaria' => [23.9571, 91.1119],
        'Chandpur' => [23.2333, 90.6667],
        'Noakhali' => [22.8696, 91.0998],
        'Lakshmipur' => [22.9425, 90.8412],
        'Khagrachhari' => [23.1193, 91.9847],
        'Rangamati' => [22.7324, 92.2985],
        'Bandarban' => [22.1953, 92.2184],

        'Sylhet' => [24.8949, 91.8687],
        'Moulvibazar' => [24.4829, 91.7774],
        'Habiganj' => [24.3749, 91.4155],
        'Sunamganj' => [25.0658, 91.3950],

        'Rajshahi' => [24.3745, 88.6042],
        'Bogura' => [24.8465, 89.3777],
        'Bogra' => [24.8465, 89.3777],
        'Pabna' => [24.0064, 89.2372],
        'Sirajganj' => [24.4534, 89.7008],
        'Naogaon' => [24.7936, 88.9318],
        'Natore' => [24.4206, 89.0003],
        'Chapai Nawabganj' => [24.5965, 88.2775],
        'Joypurhat' => [25.1015, 89.0277],

        'Khulna' => [22.8456, 89.5403],
        'Jashore' => [23.1664, 89.2182],
        'Jessore' => [23.1664, 89.2182],
        'Kushtia' => [23.9013, 89.1205],
        'Satkhira' => [22.7185, 89.0705],
        'Bagerhat' => [22.6516, 89.7859],
        'Jhenaidah' => [23.5448, 89.1539],
        'Chuadanga' => [23.6402, 88.8418],
        'Magura' => [23.4873, 89.4198],
        'Meherpur' => [23.7622, 88.6318],
        'Narail' => [23.1725, 89.5127],

        'Barishal' => [22.7010, 90.3535],
        'Barisal' => [22.7010, 90.3535],
        'Patuakhali' => [22.3596, 90.3299],
        'Bhola' => [22.6859, 90.6481],
        'Pirojpur' => [22.5841, 89.9720],
        'Barguna' => [22.0953, 90.1121],
        'Jhalokati' => [22.6406, 90.1987],

        'Rangpur' => [25.7439, 89.2752],
        'Dinajpur' => [25.6217, 88.6355],
        'Kurigram' => [25.8054, 89.6362],
        'Gaibandha' => [25.3288, 89.5430],
        'Nilphamari' => [25.9318, 88.8560],
        'Lalmonirhat' => [25.9165, 89.4532],
        'Thakurgaon' => [26.0337, 88.4617],
        'Panchagarh' => [26.3411, 88.5542],

        'Mymensingh' => [24.7471, 90.4203],
        'Jamalpur' => [24.9375, 89.9378],
        'Netrokona' => [24.8709, 90.7279],
        'Sherpur' => [25.0205, 90.0153],
    ];

    /**
     * Resolve latitude and longitude for a given district name or fallback.
     */
    public static function getCoordinates(string $district): ?array
    {
        $clean = trim($district);
        foreach (self::$districtCoordinates as $name => $coords) {
            if (strcasecmp($name, $clean) === 0 || stripos($clean, $name) !== false) {
                return $coords;
            }
        }

        return self::$districtCoordinates['Dhaka'];
    }

    /**
     * Haversine formula to compute great-circle distance between two GPS coordinates in kilometers.
     */
    public static function haversineDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadiusKm * $c;

        // Apply 1.25 road curvature multiplier to approximate realistic driving distance on BD road network
        $roadDistance = $distance * 1.25;

        return round($roadDistance, 1);
    }

    /**
     * Calculate distance between patient (either GPS lat/lon or District) and target entity.
     *
     * @param  float|null  $patientLat  Patient GPS latitude
     * @param  float|null  $patientLon  Patient GPS longitude
     * @param  string|null  $patientDistrict  Patient District name
     * @param  string  $targetDistrict  Target entity (Doctor / Ambulance) district
     * @param  float|null  $targetLat  Specific GPS lat of target
     * @param  float|null  $targetLon  Specific GPS lon of target
     * @return array ['distance_km' => float, 'time_minutes' => int, 'text' => string]
     */
    public static function calculateDistance(
        ?float $patientLat,
        ?float $patientLon,
        ?string $patientDistrict,
        string $targetDistrict,
        ?float $targetLat = null,
        ?float $targetLon = null,
        bool $isAmbulance = false
    ): array {
        // Resolve patient coordinates
        if ($patientLat === null || $patientLon === null) {
            $pCoords = self::getCoordinates($patientDistrict ?: 'Dhaka');
            $patientLat = $pCoords[0];
            $patientLon = $pCoords[1];
            $isSameDistrict = ! empty($patientDistrict) && (strcasecmp($patientDistrict, $targetDistrict) === 0 || stripos($targetDistrict, $patientDistrict) !== false);
        } else {
            $isSameDistrict = false;
        }

        // Resolve target coordinates
        if ($targetLat === null || $targetLon === null) {
            $tCoords = self::getCoordinates($targetDistrict);
            $targetLat = $tCoords[0];
            $targetLon = $tCoords[1];
        }

        $distanceKm = self::haversineDistanceKm($patientLat, $patientLon, $targetLat, $targetLon);

        // If in exact same district and no GPS provided, provide reasonable local intra-city distance
        if ($isSameDistrict && $distanceKm < 1.0) {
            $distanceKm = $isAmbulance ? 3.5 : 4.0;
        }

        // Estimate speed: Emergency ambulance ~35-45 km/h with siren, Patient car/cng ~25 km/h
        $avgSpeedKmH = $isAmbulance ? 35.0 : 25.0;
        $estimatedMinutes = max(5, (int) round(($distanceKm / $avgSpeedKmH) * 60));

        return [
            'distance_km' => $distanceKm,
            'time_minutes' => $estimatedMinutes,
            'time_text' => $estimatedMinutes < 60 ? "{$estimatedMinutes} মিনিট" : floor($estimatedMinutes / 60).' ঘণ্টা '.($estimatedMinutes % 60).' মি.',
            'distance_text' => "{$distanceKm} কি.মি.",
        ];
    }
}
