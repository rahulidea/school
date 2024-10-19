<?php
namespace Database\Seeders;

use App\Models\Lga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class LgasTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('lgas')->delete();

        $state_id = [
            1, 1, 1, // Andhra Pradesh
            2, 2, 2, // Arunachal Pradesh
            3, 3, 3, // Assam
            4, 4, 4, // Bihar
            5, 5, 5, // Chhattisgarh
            6, 6, 6, // Goa
            7, 7, 7, // Gujarat
            8, 8, 8, // Haryana
            9, 9, 9, // Himachal Pradesh
            10, 10, 10, // Jharkhand
            11, 11, 11, // Karnataka
            12, 12, 12, // Kerala
            13, 13, 13, // Madhya Pradesh
            14, 14, 14, // Maharashtra
            15, 15, 15, // Manipur
            16, 16, 16, // Mizoram
            17, 17, 17, // Nagaland
            18, 18, 18, // Odisha
            19, 19, 19, // Punjab
            20, 20, 20, // Rajasthan
            21, 21, 21, // Sikkim
            22, 22, 22, // Tamil Nadu
            23, 23, 23, // Telangana
            24, 24, 24, // Uttar Pradesh
            25, 25, 25, // Uttarakhand
            26, 26, 26, // West Bengal
            27, 27, 27, // Delhi
            28, 28, 28  // Jammu and Kashmir
        ];
        
        $lgas = [
            "Visakhapatnam", "Vijayawada", "Guntur", // Andhra Pradesh
            "Itanagar", "Naharlagun", "Pasighat", // Arunachal Pradesh
            "Guwahati", "Dibrugarh", "Silchar", // Assam
            "Patna", "Gaya", "Bhagalpur", // Bihar
            "Raipur", "Bilaspur", "Durg", // Chhattisgarh
            "Panaji", "Margao", "Mapusa", // Goa
            "Ahmedabad", "Surat", "Vadodara", // Gujarat
            "Chandigarh", "Faridabad", "Gurgaon", // Haryana
            "Shimla", "Dharamshala", "Kullu", // Himachal Pradesh
            "Ranchi", "Jamshedpur", "Dhanbad", // Jharkhand
            "Bengaluru", "Mysuru", "Mangaluru", // Karnataka
            "Thiruvananthapuram", "Kochi", "Kozhikode", // Kerala
            "Bhopal", "Indore", "Gwalior", // Madhya Pradesh
            "Mumbai", "Pune", "Nagpur", // Maharashtra
            "Imphal", "Shillong", "Churachandpur", // Manipur
            "Aizawl", "Lunglei", "Saiha", // Mizoram
            "Kohima", "Dimapur", "Mokokchung", // Nagaland
            "Bhubaneswar", "Cuttack", "Puri", // Odisha
            "Amritsar", "Ludhiana", "Jalandhar", // Punjab
            "Jaipur", "Udaipur", "Jodhpur", // Rajasthan
            "Gangtok", "Namchi", "Gyalshing", // Sikkim
            "Chennai", "Coimbatore", "Madurai", // Tamil Nadu
            "Hyderabad", "Warangal", "Nizamabad", // Telangana
            "Agra", "Lucknow", "Kanpur", // Uttar Pradesh
            "Dehradun", "Haridwar", "Rudrapur", // Uttarakhand
            "Kolkata", "Siliguri", "Howrah", // West Bengal
            "Delhi", "New Delhi", "Dwarka", // Delhi
            "Srinagar", "Jammu", "Leh" // Jammu and Kashmir
        ];

        for($i=0; $i<count($lgas); $i++){
            Lga::create(['state_id' => $state_id[$i], 'name' => $lgas[$i]]);
        }
    }

}
