<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorChamber;
use App\Services\GeoDistanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicDoctorController extends Controller
{
    /**
     * Complete dataset of 8 Divisions, 64 Districts, and Upazilas of Bangladesh.
     */
    public static function getBangladeshLocations(): array
    {
        return Cache::remember('bd_health_locations', 3600, function () {
            return [
                'Dhaka' => [
                    'Dhaka' => ['ধানমন্ডি (Dhanmondi)', 'মিরপুর (Mirpur)', 'উত্তরা (Uttara)', 'গুলশান (Gulshan)', 'মোহাম্মদপুর (Mohammadpur)', 'মতিঝিল (Motijheel)', 'সাভার (Savar)', 'ধামরাই (Dhamrai)', 'কেরানীগঞ্জ (Keraniganj)', 'নবাবগঞ্জ (Nawabganj)', 'দোহার (Dohar)', 'তেজগাঁও (Tejgaon)', 'বাড্ডা (Badda)', 'খিলগাঁও (Khilgaon)', 'যাত্রাবাড়ী (Jatrabari)'],
                    'Gazipur' => ['গাজীপুর সদর (Gazipur Sadar)', 'কালিয়াকৈর (Kaliakair)', 'কাপাসিয়া (Kapasia)', 'শ্রীপুর (Sreepur)', 'কালীগঞ্জ (Kaliganj)', 'টঙ্গী (Tongi)'],
                    'Narayanganj' => ['নারায়ণগঞ্জ সদর (Narayanganj Sadar)', 'বন্দর (Bandar)', 'রূপগঞ্জ (Rupganj)', 'সোনারগাঁ (Sonargaon)', 'আড়াইহাজার (Araihazar)'],
                    'Tangail' => ['টাঙ্গাইল সদর (Tangail Sadar)', 'মির্জাপুর (Mirzapur)', 'গোপালপুর (Gopalpur)', 'সখিপুর (Sakhipur)', 'মধুপুর (Madhupur)', 'ঘাটাইল (Ghatail)', 'কালিহাতী (Kalihati)', 'নাগরপুর (Nagarpur)', 'দেলদুয়ার (Delduar)', 'ভুঞাপুর (Bhuapur)', 'বাসাইল (Basail)', 'ধনবাড়ী (Dhanbari)'],
                    'Faridpur' => ['ফরিদপুর সদর (Faridpur Sadar)', 'বোয়ালমারী (Boalmari)', 'আলফাডাঙ্গা (Alfadanga)', 'মধুখালী (Madhukhali)', 'ভাঙ্গা (Bhanga)', 'নগরকান্দা (Nagarkanda)', 'চরভদ্রাসন (Charbhadrasan)', 'সদরপুর (Sadarpur)', 'সালথা (Saltha)'],
                    'Manikganj' => ['মানিকগঞ্জ সদর (Manikganj Sadar)', 'সিংগাইর (Singair)', 'সাটুরিয়া (Saturia)', 'শিবালয় (Shivalaya)', 'ঘিওর (Ghior)', 'হরিরামপুর (Harirampur)', 'দৌলতপুর (Daulatpur)'],
                    'Munshiganj' => ['মুন্সীগঞ্জ সদর (Munshiganj Sadar)', 'টংগীবাড়ী (Tongibari)', 'সিরাজদিখান (Sirajdikhan)', 'লৌহজং (Lohajang)', 'শ্রীনগর (Sreenagar)', 'গজারিয়া (Gazaria)'],
                    'Narsingdi' => ['নরসিংদী সদর (Narsingdi Sadar)', 'পলাশ (Palash)', 'শিবপুর (Shibpur)', 'বেলাবো (Belabo)', 'মনোহরদী (Monohardi)', 'রায়পুরা (Raipura)'],
                    'Gopalganj' => ['গোপালগঞ্জ সদর (Gopalganj Sadar)', 'কাশিয়ানী (Kashiani)', 'কোটালীপাড়া (Kotalipara)', 'মুকসুদপুর (Muksudpur)', 'টুঙ্গিপাড়া (Tungipara)'],
                    'Kishoreganj' => ['কিশোরগঞ্জ সদর (Kishoreganj Sadar)', 'ভৈরব (Bhairab)', 'বাজিতপুর (Bajitpur)', 'কটিয়াদী (Katiadi)', 'করিমগঞ্জ (Karimganj)', 'নিকলী (Nikli)', 'তাড়াইল (Tarail)', 'হোসেনপুর (Hossainpur)', 'পাকুন্দিয়া (Pakundia)', 'কুলিয়ারচর (Kuliarchar)', 'ইটনা (Itna)', 'মিঠামইন (Mithamain)', 'অষ্টগ্রাম (Austagram)'],
                    'Madaripur' => ['মাদারীপুর সদর (Madaripur Sadar)', 'শিবচর (Shivchar)', 'কালকিনি (Kalkini)', 'রাজৈর (Rajoir)', 'ডাসার (Dasar)'],
                    'Rajbari' => ['রাজবাড়ী সদর (Rajbari Sadar)', 'গোয়ালন্দ (Goalanda)', 'পাংশা (Pangsha)', 'বালিয়াকান্দি (Baliakandi)', 'কালুখালী (Kalukhali)'],
                    'Shariatpur' => ['শরীয়তপুর সদর (Shariatpur Sadar)', 'জাজিরা (Zajira)', 'নড়িয়া (Naria)', 'ভেদরগঞ্জ (Bhedarganj)', 'ডামুড্যা (Damudya)', 'গোসাইরহাট (Gosairhat)'],
                ],
                'Chittagong' => [
                    'Chittagong' => ['কোতোয়ালী (Kotwali)', 'পাঁচলাইশ (Panchlaish)', 'খুলশী (Khulshi)', 'হালিশহর (Halishahar)', 'ডবলমুরিং (Double Mooring)', 'পাহাড়তলী (Pahartali)', 'পতেঙ্গা (Patenga)', 'চন্দনাইশ (Chandanpukur)', 'রাউজান (Raozan)', 'হাটহাজারী (Hathazari)', 'সীতাকুণ্ড (Sitakunda)', 'মীরসরাই (Mirsarai)', 'পটিয়া (Patiya)', 'বোয়ালখালী (Boalkhali)', 'আনোয়ারা (Anwara)', 'ফটিকছড়ি (Fatikchhari)', 'বাঁশখালী (Banshkhali)', 'লোহাগাড়া (Lohagara)', 'সাতকানিয়া (Satkania)', 'সন্দ্বীপ (Sandwip)'],
                    'Cox\'s Bazar' => ['কক্সবাজার সদর (Cox\'s Bazar Sadar)', 'চকরিয়া (Chakaria)', 'মহেশখালী (Maheshkhali)', 'টেকনাফ (Teknaf)', 'উখিয়া (Ukhiya)', 'রামু (Ramu)', 'পেকুয়া (Pekua)', 'কুতুবদিয়া (Kutubdia)'],
                    'Cumilla' => ['কুমিল্লা আদর্শ সদর (Cumilla Adarsha Sadar)', 'চৌদ্দগ্রাম (Chauddagram)', 'দাউদকান্দি (Daudkandi)', 'লাকসাম (Laksam)', 'দেবীদ্বার (Debidwar)', 'মুরাদনগর (Muradnagar)', 'বরুড়া (Barura)', 'বুড়িচং (Burichang)', 'চান্দিনা (Chandina)', 'হোমনা (Homna)', 'ব্রাহ্মণপাড়া (Brahmanpara)', 'তিতাস (Titas)', 'মেঘনা (Meghna)', 'নাঙ্গলকোট (Nangalkot)', 'লালমাই (Lalmai)'],
                    'Brahmanbaria' => ['ব্রাহ্মণবাড়িয়া সদর (Brahmanbaria Sadar)', 'আশুগঞ্জ (Ashuganj)', 'সরাইল (Sarail)', 'কসবা (Kasba)', 'নবীনগর (Nabinagar)', 'নাসিরনগর (Nasirnagar)', 'বাঞ্ছারামপুর (Bancharampur)', 'আখাউড়া (Akhaura)', 'বিজয়নগর (Bijoynagar)'],
                    'Chandpur' => ['চাঁদপুর সদর (Chandpur Sadar)', 'হাজীগঞ্জ (Hajiganj)', 'ফরিদগঞ্জ (Faridganj)', 'মতলব উত্তর (Matlab North)', 'মতলব দক্ষিণ (Matlab South)', 'শাহরাস্তি (Shahrasti)', 'কচুয়া (Kachua)', 'হাইমচর (Haimchar)'],
                    'Feni' => ['ফেনী সদর (Feni Sadar)', 'দাগনভূঞা (Daganbhuiyan)', 'সোনাগাজী (Sonagazi)', 'ছাগলনাইয়া (Chhagalnaiya)', 'পরশুরাম (Parshuram)', 'ফুলগাজী (Fulgazi)'],
                    'Lakshmipur' => ['লক্ষ্মীপুর সদর (Lakshmipur Sadar)', 'রায়পুর (Raipur)', 'রামগঞ্জ (Ramganj)', 'রামগতি (Ramgati)', 'কমলনগর (Kamalnagar)'],
                    'Noakhali' => ['নোয়াখালী সদর (Noakhali Sadar)', 'বেগমগঞ্জ (Begumganj)', 'চাটখিল (Chatkhil)', 'কোম্পানীগঞ্জ (Companiganj)', 'হাতিয়া (Hatiya)', 'সেনবাগ (Senbagh)', 'সোনাইমুড়ী (Sonaimuri)', 'সুবর্ণচর (Subarnachar)', 'কবিরহাট (Kabirhat)'],
                    'Bandarban' => ['বান্দরবান সদর (Bandarban Sadar)', 'রুমা (Ruma)', 'থানচি (Thanchi)', 'রোয়াংছড়ি (Rowangchhari)', 'লামা (Lama)', 'আলীকদম (Alikadam)', 'নাইক্ষ্যংছড়ি (Naikhongchhari)'],
                    'Khagrachhari' => ['খাগড়াছড়ি সদর (Khagrachhari Sadar)', 'দীঘিনালা (Dighinala)', 'পানছড়ি (Panchhari)', 'মহালছড়ি (Mahalchhari)', 'মাটিরাঙ্গা (Matiranga)', 'মানিকছড়ি (Manikchhari)', 'রামগড় (Ramgarh)', 'লক্ষ্মীছড়ি (Lakshmichhari)', 'গুইমারা (Guimara)'],
                    'Rangamati' => ['রাঙ্গামাটি সদর (Rangamati Sadar)', 'কাপ্তাই (Kaptai)', 'বাঘাইছড়ি (Baghaichhari)', 'বরকল (Barkal)', 'লংগদু (Langadu)', 'নানিয়ারচর (Naniarchar)', 'জুরাইছড়ি (Juraichhari)', 'রাজস্থলী (Rajasthali)', 'বিলাইছড়ি (Belaichhari)', 'কাউখালী (Kaukhali)'],
                ],
                'Rajshahi' => [
                    'Rajshahi' => ['বোয়ালিয়া (Boalia)', 'রাজপাড়া (Rajpara)', 'মতিহার (Motihar)', 'শাহ মখদুম (Shah Makhdum)', 'পবা (Paba)', 'গোদাগাড়ী (Godagari)', 'তানোর (Tanor)', 'বাগমারা (Bagmara)', 'মোহনপুর (Mohanpur)', 'পুঠিয়া (Puthia)', 'বাঘা (Bagha)', 'চারঘাট (Charghat)', 'দুর্গাপুর (Durgapur)'],
                    'Bogra' => ['বগুড়া সদর (Bogra Sadar)', 'শেরপুর (Sherpur)', 'শিবগঞ্জ (Shibganj)', 'শাজাহানপুর (Shajahanpur)', 'গাবতলী (Gabtali)', 'দুপচাঁচিয়া (Dupchanchia)', 'কাহালু (Kahalu)', 'নন্দীগ্রাম (Nandigram)', 'ধুনট (Dhunat)', 'আদমদীঘি (Adamdighi)', 'সোনাতলা (Sonatala)', 'সারিয়াকান্দি (Sariakandi)'],
                    'Pabna' => ['পাবনা সদর (Pabna Sadar)', 'ঈশ্বরদী (Ishwardi)', 'সাঁথিয়া (Santhia)', 'সুজানগর (Sujanagar)', 'বেড়া (Bera)', 'চাটমোহর (Chatmohar)', 'ফরিদপুর (Faridpur)', 'ভাঙ্গুড়া (Bhangura)', 'আটঘরিয়া (Atgharia)'],
                    'Sirajganj' => ['সিরাজগঞ্জ সদর (Sirajganj Sadar)', 'শাহজাদপুর (Shahjadpur)', 'উল্লাপাড়া (Ullapara)', 'বেলকুচি (Belkuchi)', 'রায়গঞ্জ (Raiganj)', 'তাড়াশ (Tarash)', 'কাজীপুর (Kazipur)', 'কামারখন্দ (Kamarkhanda)', 'চৌহালী (Chauhali)'],
                    'Naogaon' => ['নওগাঁ সদর (Naogaon Sadar)', 'পত্নীতলা (Patnitala)', 'ধামইরহাট (Dhamoirhat)', 'মহাদেবপুর (Mohadevpur)', 'মান্দা (Manda)', 'বদলগাছী (Badalgachhi)', 'রানীনগর (Raninagar)', 'আত্রাই (Atrai)', 'নিয়ামতপুর (Niamatpur)', 'সাপাহার (Sapahar)', 'পোরশা (Porsha)'],
                    'Natore' => ['নাটোর সদর (Natore Sadar)', 'বড়াইগ্রাম (Baraigram)', 'সিংড়া (Singra)', 'গুরুদাসপুর (Gurudaspur)', 'লালপুর (Lalpur)', 'বাগাতিপাড়া (Bagatipara)', 'নলডাঙ্গা (Naldanga)'],
                    'Chapai Nawabganj' => ['চাঁপাইনবাবগঞ্জ সদর (Chapai Sadar)', 'শিবগঞ্জ (Shibganj)', 'গোমস্তাপুর (Gomastapur)', 'নাচোল (Nachole)', 'ভোলাহাট (Bholahat)'],
                    'Joypurhat' => ['জয়পুরহাট সদর (Joypurhat Sadar)', 'পাঁচবিবি (Panchbibi)', 'কালাই (Kalai)', 'ক্ষেতলাল (Khetlal)', 'আক্কেলপুর (Akkelpur)'],
                ],
                'Khulna' => [
                    'Khulna' => ['খুলনা সদর (Khulna Sadar)', 'সোনাডাঙ্গা (Sonadanga)', 'খালিশপুর (Khalishpur)', 'দৌলতপুর (Daulatpur)', 'খান জাহান আলী (Khan Jahan Ali)', 'ডুমুরিয়া (Dumuria)', 'বটিয়াঘাটা (Batiaghata)', 'রূপসা (Rupsha)', 'তেরখাদা (Terokhada)', 'দিঘলিয়া (Dighalia)', 'ফুলতলা (Phultala)', 'পাইকগাছা (Paikgachha)', 'কয়রা (Koyra)', 'দাকোপ (Dacope)'],
                    'Jashore' => ['যশোর সদর (Jashore Sadar)', 'ঝিকরগাছা (Jhikargachha)', 'শার্শা (Sharsha)', 'মনিরামপুর (Manirampur)', 'কেশবপুর (Keshabpur)', 'চৌগাছা (Chowgachha)', 'বাঘারপাড়া (Bagherpara)', 'অভয়নগর (Abhaynagar)'],
                    'Kushtia' => ['কুষ্টিয়া সদর (Kushtia Sadar)', 'কুমারখালী (Kumarkhali)', 'ভেড়ামারা (Bheramara)', 'মিরপুর (Mirpur)', 'খোকসা (Khoksa)', 'দৌলতপুর (Daulatpur)'],
                    'Satkhira' => ['সাতক্ষীরা সদর (Satkhira Sadar)', 'কলারোয়া (Kalaroa)', 'তালা (Tala)', 'কালীগঞ্জ (Kaliganj)', 'শ্যামনগর (Shyamnagar)', 'আশাশুনি (Assasuni)', 'দেবহাটা (Debhata)'],
                    'Bagerhat' => ['বাগেরহাট সদর (Bagerhat Sadar)', 'ফকিরহাট (Fakirhat)', 'মোল্লাহাট (Mollahat)', 'রামপাল (Rampal)', 'মোংলা (Mongla)', 'কচুয়া (Kachua)', 'মোরেলগঞ্জ (Morrelganj)', 'শরণখোলা (Sarankhola)', 'চিতলমারী (Chitalmari)'],
                    'Chuadanga' => ['চুয়াডাঙ্গা সদর (Chuadanga Sadar)', 'আলমডাঙ্গা (Alamdanga)', 'দামুড়হুদা (Damurhuda)', 'জীবননগর (Jibannagar)'],
                    'Jhenaidah' => ['ঝিনাইদহ সদর (Jhenaidah Sadar)', 'শৈলকুপা (Sailkupa)', 'হরিণাকুণ্ডু (Harinakundu)', 'কালীগঞ্জ (Kaliganj)', 'কোটচাঁদপুর (Kotchandpur)', 'মহেশপুর (Maheshpur)'],
                    'Magura' => ['মাগুরা সদর (Magura Sadar)', 'শ্রীপুর (Sreepur)', 'শালিখা (Shalikha)', 'মহম্মদপুর (Mohammadpur)'],
                    'Meherpur' => ['মেহেরপুর সদর (Meherpur Sadar)', 'গাংনী (Gangni)', 'মুজিবনগর (Mujibnagar)'],
                    'Narail' => ['নড়াইল সদর (Narail Sadar)', 'লোহাগড়া (Lohagara)', 'কালিয়া (Kalia)'],
                ],
                'Sylhet' => [
                    'Sylhet' => ['সিলেট সদর (Sylhet Sadar)', 'কোতোয়ালী (Kotwali)', 'দক্ষিণ সুরমা (South Surma)', 'গোলাপগঞ্জ (Golapganj)', 'বিয়ানীবাজার (Beanibazar)', 'জৈন্তাপুর (Jaintiapur)', 'কানাইঘাট (Kanaighat)', 'জকিগঞ্জ (Zakiganj)', 'বিশ্বনাথ (Bishwanath)', 'বালাগঞ্জ (Balaganj)', 'ফেঞ্চুগঞ্জ (Fenchuganj)', 'কোম্পানীগঞ্জ (Companiganj)', 'গোয়াইনঘাট (Gowainghat)', 'ওসমানীনগর (Osmani Nagar)'],
                    'Moulvibazar' => ['মৌলভীবাজার সদর (Moulvibazar Sadar)', 'শ্রীমঙ্গল (Sreemangal)', 'কমলগঞ্জ (Kamalganj)', 'কুলাউড়া (Kulaura)', 'বড়লেখা (Barlekha)', 'রাজনগর (Rajnagar)', 'জুড়ী (Juri)'],
                    'Habiganj' => ['হবিগঞ্জ সদর (Habiganj Sadar)', 'মাধবপুর (Madhabpur)', 'চুনারুঘাট (Chunarughat)', 'বাহুবল (Bahubal)', 'নবীগঞ্জ (Nabiganj)', 'বানিয়াচং (Baniachong)', 'লাখাই (Lakhai)', 'আজমিরীগঞ্জ (Ajmiriganj)', 'শায়েস্তাগঞ্জ (Shayestaganj)'],
                    'Sunamganj' => ['সুনামগঞ্জ সদর (Sunamganj Sadar)', 'ছাতক (Chhatak)', 'জগন্নাথপুর (Jagannathpur)', 'তাহিরপুর (Tahirpur)', 'ধর্মপাশা (Dharampasha)', 'বিশ্বম্ভরপুর (Bishwamvarpur)', 'দিরাই (Dirai)', 'শাল্লা (Shalla)', 'দোয়ারাবাজার (Dowarabazar)', 'জামালগঞ্জ (Jamalganj)', 'শান্তিগঞ্জ (Shantiganj)'],
                ],
                'Barisal' => [
                    'Barisal' => ['বরিশাল সদর (Barisal Sadar)', 'বাবুগঞ্জ (Babuganj)', 'উজিরপুর (Wazirpur)', 'বাকেরগঞ্জ (Bakerganj)', 'গৌরনদী (Gournadi)', 'আগৈলঝাড়া (Agailjhara)', 'বানারীপাড়া (Banaripara)', 'মুলাদী (Muladi)', 'হিজলা (Hizla)', 'মেহেন্দীগঞ্জ (Mehendiganj)'],
                    'Bhola' => ['ভোলা সদর (Bhola Sadar)', 'বোরহানউদ্দিন (Borhanuddin)', 'দৌলতখান (Daulatkhan)', 'লালমোহন (Lalmohan)', 'চরফ্যাশন (Char Fasson)', 'তজুমদ্দিন (Tazumuddin)', 'মনপুরা (Monpura)'],
                    'Jhalokati' => ['ঝালকাঠি সদর (Jhalokati Sadar)', 'নলছিটি (Nalchity)', 'রাজাপুর (Rajapur)', 'কাঠালিয়া (Kathalia)'],
                    'Patuakhali' => ['পটুয়াখালী সদর (Patuakhali Sadar)', 'বাউফল (Bauphal)', 'গলাচিপা (Galachipa)', 'দশমিনা (Dashmina)', 'কলাপাড়া (Kalapara)', 'মির্জাগঞ্জ (Mirzaganj)', 'দুমকি (Dumki)', 'রাঙ্গাবালী (Rangabali)'],
                    'Pirojpur' => ['পিরোজপুর সদর (Pirojpur Sadar)', 'ভাণ্ডারিয়া (Bhandaria)', 'মঠবাড়িয়া (Mathbaria)', 'নাজিরপুর (Nazirpur)', 'কাউখালী (Kawkhali)', 'নেছারাবাদ (Nesarabad)', 'ইন্দুরকানী (Indurkani)'],
                    'Barguna' => ['বরগুনা সদর (Barguna Sadar)', 'আমতলী (Amtali)', 'পাথরঘাটা (Patharghata)', 'বেতাগী (Betagi)', 'বামনা (Bamna)', 'তালতলী (Taltali)'],
                ],
                'Rangpur' => [
                    'Rangpur' => ['রংপুর সদর (Rangpur Sadar)', 'পীরগঞ্জ (Pirganj)', 'মিঠাপুকুর (Mithapukur)', 'বদরগঞ্জ (Badarganj)', 'কাউনিয়া (Kaunia)', 'গঙ্গাচড়া (Gangachhara)', 'তারাগঞ্জ (Taraganj)', 'পীরগাছা (Pirgachha)'],
                    'Dinajpur' => ['দিনাজপুর সদর (Dinajpur Sadar)', 'বিরামপুর (Birampur)', 'বীরগঞ্জ (Birganj)', 'বোচাগঞ্জ (Bochaganj)', 'ফুলবাড়ী (Phulbari)', 'কাহারোল (Kaharole)', 'হাকিমপুর (Hakirmpur)', 'ঘোড়াঘাট (Ghoraghat)', 'চিরিরবন্দর (Chirirbandar)', 'নবাবগঞ্জ (Nawabganj)', 'পার্বতীপুর (Parbatipur)', 'খানসামা (Khansama)', 'বিরল (Biral)'],
                    'Kurigram' => ['কুড়িগ্রাম সদর (Kurigram Sadar)', 'নাগেশ্বরী (Nageshwari)', 'ভুরুঙ্গামারী (Bhurungamari)', 'উলিপুর (Ulipur)', 'চিলমারী (Chilmari)', 'রৌমারী (Roumari)', 'রাজিবপুর (Rajibpur)', 'ফুলবাড়ী (Phulbari)', 'রাজারহাট (Rajarhat)'],
                    'Gaibandha' => ['গাইবান্ধা সদর (Gaibandha Sadar)', 'গোবিন্দগঞ্জ (Gobindaganj)', 'পলাশবাড়ী (Palashbari)', 'সাদুল্লাপুর (Sadullapur)', 'সুন্দরগঞ্জ (Sundarganj)', 'সাঘাটা (Saghata)', 'ফুলছড়ি (Fulchhari)'],
                    'Nilphamari' => ['নীলফামারী সদর (Nilphamari Sadar)', 'সৈয়দপুর (Saidpur)', 'ডোমার (Domar)', 'ডিমলা (Dimla)', 'জলঢাকা (Jaldhaka)', 'কিশোরগঞ্জ (Kishoreganj)'],
                    'Lalmonirhat' => ['লালমনিরহাট সদর (Lalmonirhat Sadar)', 'পাটগ্রাম (Patgram)', 'হাতীবান্ধা (Hatibandha)', 'কালীগঞ্জ (Kaliganj)', 'আদিতমারী (Aditmari)'],
                    'Thakurgaon' => ['ঠাকুরগাঁও সদর (Thakurgaon Sadar)', 'পীরগঞ্জ (Pirganj)', 'রাণীশংকৈল (Ranisankail)', 'হরিপুর (Haripur)', 'বালিয়াডাঙ্গী (Baliadangi)'],
                    'Panchagarh' => ['পঞ্চগড় সদর (Panchagarh Sadar)', 'তেঁতুলিয়া (Tetulia)', 'বোদা (Boda)', 'দেবীগঞ্জ (Debiganj)', 'আটোয়ারী (Atwari)'],
                ],
                'Mymensingh' => [
                    'Mymensingh' => ['ময়মনসিংহ সদর (Mymensingh Sadar)', 'মুক্তাগাছা (Muktagachha)', 'ফুলবাড়িয়া (Fulbaria)', 'ত্রিশাল (Trishal)', 'ভালুকা (Bhaluka)', 'গফরগাঁও (Gafargaon)', 'ঈশ্বরগঞ্জ (Ishwarganj)', 'নান্দাইল (Nandail)', 'গৌরীপুর (Gauripur)', 'হালুয়াঘাট (Haluaghat)', 'ধোবাউড়া (Dhobaura)', 'ফুলপুর (Phulpur)', 'তারাকান্দা (Tarakanda)'],
                    'Jamalpur' => ['জামালপুর সদর (Jamalpur Sadar)', 'সরিষাবাড়ী (Sarishabari)', 'মেলান্দহ (Melandaha)', 'ইসলামপুর (Islampur)', 'দেওয়ানগঞ্জ (Dewanganj)', 'মাদারগঞ্জ (Madarganj)', 'বকশীগঞ্জ (Bakhshiganj)'],
                    'Netrokona' => ['নেত্রকোণা সদর (Netrokona Sadar)', 'দুর্গাপুর (Durgapur)', 'কলমাকান্দা (Kalmakanda)', 'কেন্দুয়া (Kendua)', 'মদন (Madan)', 'মোহনগঞ্জ (Mohanganj)', 'পূর্বধলা (Purbadhala)', 'বারহাট্টা (Barhatta)', 'আটপাড়া (Atpara)', 'খালিয়াজুরী (Khaliajuri)'],
                    'Sherpur' => ['শেরপুর সদর (Sherpur Sadar)', 'নকলা (Nakla)', 'নালিতাবাড়ী (Nalitabari)', 'শ্রীবরদী (Sreebardi)', 'ঝিনাইগাতী (Jhenaigati)'],
                ],
            ];
        });
    }

    /**
     * Get list of approved doctors with search & multi-tier filters (Cached for 1k-5k Concurrency).
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'public_doctors_'.md5(json_encode($request->all()));

        $result = Cache::remember($cacheKey, 30, function () use ($request) {
            $query = Doctor::approved()
                ->select([
                    'id', 'name', 'slug', 'specialization', 'designation', 'qualifications',
                    'avatar', 'phone', 'chamber_address', 'city', 'district', 'upazila', 'division',
                    'consultation_fee', 'telemedicine_fee', 'experience_years', 'available_days',
                    'is_approved', 'is_verified', 'is_featured', 'is_emergency_available',
                    'free_consultation_enabled', 'free_days', 'free_start_time', 'free_end_time',
                    'free_patient_limit', 'free_chamber_address', 'free_service_note',
                    'bmdc_number', 'telemedicine_link', 'membership_plan', 'membership_expires_at',
                ])
                ->withCount('appointments')
                ->with(['chambers' => function ($q) {
                    $q->where('is_active', true)->orderByDesc('is_primary')->orderBy('id');
                }])
                ->orderByRaw("CASE WHEN membership_plan = 'vip' THEN 3 WHEN membership_plan = 'pro' THEN 2 WHEN is_featured = 1 THEN 1 ELSE 0 END DESC");

            // Division Filter
            if ($request->filled('division') && $request->input('division') !== 'all') {
                $query->where('division', $request->input('division'));
            }

            // 64 District Filter
            if ($request->filled('district') && $request->input('district') !== 'all') {
                $query->where('district', $request->input('district'));
            }

            // Upazila / Thana Filter
            if ($request->filled('upazila') && $request->input('upazila') !== 'all') {
                $query->where(function ($q) use ($request) {
                    $u = $request->input('upazila');
                    $q->where('upazila', 'like', "%{$u}%")
                        ->orWhere('chamber_address', 'like', "%{$u}%");
                });
            }

            // Specialization Filter
            if ($request->filled('specialization') && $request->input('specialization') !== 'all') {
                $specInput = $request->input('specialization');
                $category = collect(self::getStandardSpecializations())->first(function ($c) use ($specInput) {
                    return $c['key'] === $specInput || $c['name'] === $specInput;
                });

                if ($category) {
                    $query->where(function ($q) use ($category) {
                        foreach ($category['keywords'] as $kw) {
                            $q->orWhere('specialization', 'like', "%{$kw}%");
                        }
                    });
                } else {
                    $query->where('specialization', 'like', "%{$specInput}%");
                }
            }

            // Telemedicine / Online Video Consult Available
            if ($request->boolean('telemedicine_only')) {
                $query->whereNotNull('telemedicine_link');
            }

            // 24/7 Emergency Doctor Filter
            if ($request->boolean('emergency_only')) {
                $query->where('is_emergency_available', true);
            }

            // BMDC Verified Only Filter
            if ($request->boolean('verified_only')) {
                $query->where('is_verified', true);
            }

            // Free Consultation / Charity Filter
            if ($request->boolean('free_only')) {
                $query->where('free_consultation_enabled', true);
            }

            // Keyword Search (Name, Specialization, BMDC, Chamber, District, Upazila)
            if ($request->filled('search')) {
                $search = trim($request->input('search'));
                // Strip common titles
                $cleanSearch = preg_replace('/^(ডাক্তার|ডাঃ|ডা\.|ডক্টর|doctor|dr\.?|অধ্যাপক|প্রফেসর|prof\.?)\s+/ui', '', $search);
                $cleanSearch = preg_replace('/\s+(ডাক্তার|সাহেব|স্যার|এর কাছে|দেখাব|সিরিয়াল|চেম্বার)$/ui', '', $cleanSearch);
                $cleanSearch = trim($cleanSearch);

                // Normalize Bengali Unicode characters (decomposed য + nukta -> য়)
                $normSearch = str_replace(["\xE0\xA6\xAF\xE0\xA6\xBC"], ["\xE0\xA7\x9F"], $cleanSearch ?: $search);
                $tokens = array_filter(preg_split('/\s+/u', $normSearch));

                $query->where(function ($q) use ($search, $normSearch, $tokens) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$normSearch}%")
                        ->orWhere('specialization', 'like', "%{$search}%")
                        ->orWhere('chamber_address', 'like', "%{$search}%")
                        ->orWhere('bmdc_number', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('upazila', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");

                    if (! empty($tokens)) {
                        $q->orWhere(function ($subQ) use ($tokens) {
                            foreach ($tokens as $token) {
                                $vars = array_unique([
                                    $token,
                                    str_replace('ী', 'ি', $token),
                                    str_replace('ি', 'ী', $token),
                                    str_replace('ূ', 'ু', $token),
                                    str_replace('ু', 'ূ', $token),
                                    str_replace('স', 'শ', $token),
                                    str_replace('শ', 'স', $token),
                                    str_replace('ষ', 'শ', $token),
                                ]);
                                $subQ->where(function ($tokQ) use ($vars) {
                                    foreach ($vars as $v) {
                                        $tokQ->orWhere('name', 'like', "%{$v}%");
                                    }
                                });
                            }
                        });
                    }
                });
            }

            $patientDistrict = trim($request->input('patient_district', ''));
            $patientUpazila = trim($request->input('patient_upazila', ''));

            if (! empty($patientUpazila) || ! empty($patientDistrict)) {
                $scoreParts = [];
                $bindings = [];

                if (! empty($patientUpazila)) {
                    $scoreParts[] = '(CASE WHEN (upazila LIKE ? OR chamber_address LIKE ?) THEN 1000 ELSE 0 END)';
                    $bindings[] = "%{$patientUpazila}%";
                    $bindings[] = "%{$patientUpazila}%";
                }

                if (! empty($patientDistrict)) {
                    $scoreParts[] = '(CASE WHEN (district LIKE ? OR city LIKE ? OR chamber_address LIKE ?) THEN 500 ELSE 0 END)';
                    $bindings[] = "%{$patientDistrict}%";
                    $bindings[] = "%{$patientDistrict}%";
                    $bindings[] = "%{$patientDistrict}%";
                }

                if (! empty($scoreParts)) {
                    $query->orderByRaw('('.implode(' + ', $scoreParts).') DESC', $bindings);
                }
            }

            $patientLat = $request->filled('patient_lat') ? (float) $request->input('patient_lat') : null;
            $patientLon = $request->filled('patient_lng') ? (float) $request->input('patient_lng') : null;

            $doctors = $query->get();
            $doctors->makeHidden(['portfolio_settings', 'portfolio_data', 'bio']);

            $doctorsArray = $doctors->map(function ($doc) use ($patientLat, $patientLon, $patientDistrict) {
                $docArr = $doc->toArray();
                if ($patientLat || ! empty($patientDistrict)) {
                    $dist = GeoDistanceService::calculateDistance(
                        $patientLat,
                        $patientLon,
                        $patientDistrict,
                        $doc->district ?: 'Dhaka',
                        null,
                        null,
                        false
                    );
                    $docArr['distance_km'] = $dist['distance_km'];
                    $docArr['distance_text'] = $dist['distance_text'];
                    $docArr['time_text'] = $dist['time_text'];
                }

                return $docArr;
            })->all();

            $specializations = self::getStandardSpecializationsWithCounts();

            return [
                'count' => count($doctorsArray),
                'doctors' => $doctorsArray,
                'specializations' => $specializations,
                'locations' => self::getBangladeshLocations(),
            ];
        });

        return response()->json(array_merge(['success' => true], $result));
    }

    /**
     * Get locations data independently.
     */
    public function locations(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'locations' => self::getBangladeshLocations(),
        ]);
    }

    /**
     * Get single doctor by slug or ID (Cached for 60s).
     */
    public function show(string $slug): JsonResponse
    {
        $cacheKey = "doctor_profile_{$slug}";

        $result = Cache::remember($cacheKey, 60, function () use ($slug) {
            $doctor = Doctor::with(['chambers' => function ($q) {
                $q->where('is_active', true)->orderByDesc('is_primary')->orderBy('id');
            }])
                ->where(function ($query) use ($slug) {
                    $query->where('slug', $slug);
                    if (is_numeric($slug)) {
                        $query->orWhere('id', (int) $slug);
                    }
                })
                ->withCount('appointments')
                ->firstOrFail();

            $relatedDoctors = Doctor::approved()
                ->where('id', '!=', $doctor->id)
                ->where(function ($q) use ($doctor) {
                    $q->where('specialization', $doctor->specialization)
                        ->orWhere('district', $doctor->district);
                })
                ->take(5)
                ->get();

            return [
                'doctor' => $doctor,
                'related_doctors' => $relatedDoctors,
            ];
        });

        return response()->json(array_merge(['success' => true], $result));
    }

    /**
     * Get available slots for doctor on a given date (optionally for a specific chamber).
     */
    public function slots(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'chamber_id' => 'nullable|integer',
        ]);

        $doctor = Doctor::findOrFail($id);
        $date = $request->input('date');
        $chamberId = $request->input('chamber_id');

        $isFree = $doctor->isFreeOnDate($date);
        $remainingQuota = $isFree ? $doctor->getFreeConsultationRemainingQuota($date) : null;
        $slots = $doctor->getAvailableSlotsForDate($date, $chamberId ? (int) $chamberId : null);

        /** @var DoctorChamber|null $selectedChamber */
        $selectedChamber = $chamberId ? $doctor->chambers()->find($chamberId) : ($doctor->primaryChamber ?: $doctor->chambers()->first());
        $chamberFee = $selectedChamber ? $selectedChamber->consultation_fee : null;
        $fee = $isFree ? 0 : (float) ($chamberFee ?? $doctor->consultation_fee);

        return response()->json([
            'success' => true,
            'date' => $date,
            'chamber_id' => $selectedChamber?->id,
            'chamber' => $selectedChamber,
            'is_free_day' => $isFree,
            'free_patient_limit' => $doctor->free_patient_limit ?: 20,
            'remaining_quota' => $remainingQuota,
            'free_chamber_address' => $doctor->free_chamber_address ?: ($selectedChamber?->address ?: $doctor->chamber_address),
            'free_service_note' => $doctor->free_service_note,
            'fee' => $fee,
            'slots' => $slots,
        ]);
    }

    /**
     * Standard Medical Specializations Taxonomy for Bangladesh Healthcare
     */
    public static function getStandardSpecializations(): array
    {
        return [
            [
                'key' => 'medicine',
                'name' => 'মেডিসিন বিশেষজ্ঞ (General Medicine)',
                'icon' => 'icofont-stethoscope',
                'keywords' => ['মেডিসিন', 'medicine', 'ইন্টারনাল'],
            ],
            [
                'key' => 'gynecology',
                'name' => 'গাইনী ও প্রসূতিরোগ (Gynecology & Obs)',
                'icon' => 'icofont-female',
                'keywords' => ['গাইনী', 'স্ত্রী', 'gynecology', 'obstetrics', 'প্রসূতি'],
            ],
            [
                'key' => 'cardiology',
                'name' => 'হৃদরোগ ও কার্ডিওলজি (Cardiology)',
                'icon' => 'icofont-heart-beat',
                'keywords' => ['কার্ডিওলজি', 'হৃদরোগ', 'cardiology', 'হার্ট'],
            ],
            [
                'key' => 'pediatrics',
                'name' => 'শিশু ও কিশোর রোগ (Pediatrics)',
                'icon' => 'icofont-baby',
                'keywords' => ['শিশু', 'pediatric', 'pediatrics', 'নবজাতক'],
            ],
            [
                'key' => 'orthopedics',
                'name' => 'অর্থোপেডিক ও হাড়-জোড়া (Orthopedics)',
                'icon' => 'icofont-crutch',
                'keywords' => ['অর্থোপেডিক', 'হাড়', 'orthopedic', 'orthopaedics'],
            ],
            [
                'key' => 'surgery',
                'name' => 'জেনারেল ও ল্যাপারোস্কপিক সার্জারি (Surgery)',
                'icon' => 'icofont-surgeon',
                'keywords' => ['সার্জারি', 'সার্জন', 'surgery', 'surgeon'],
            ],
            [
                'key' => 'urology',
                'name' => 'কিডনি ও ইউরোলজি (Urology & Kidney)',
                'icon' => 'icofont-injection-syringe',
                'keywords' => ['ইউরোলজি', 'কিডনি', 'nephrology', 'urology', 'মূত্র'],
            ],
            [
                'key' => 'ent',
                'name' => 'নাক, কান ও গলা (ENT / Otolaryngology)',
                'icon' => 'icofont-ear',
                'keywords' => ['নাক', 'কান', 'গলা', 'ent', 'otolaryngology'],
            ],
            [
                'key' => 'dermatology',
                'name' => 'চর্ম, যৌন ও অ্যালার্জি (Dermatology)',
                'icon' => 'icofont-medical-sign',
                'keywords' => ['চর্ম', 'যৌন', 'dermatology', 'skin', 'অ্যালার্জি'],
            ],
            [
                'key' => 'neurology',
                'name' => 'নিউরোলজি ও ব্রেন (Neurology)',
                'icon' => 'icofont-brain',
                'keywords' => ['নিউরো', 'ব্রেন', 'neurology', 'স্নায়ু'],
            ],
            [
                'key' => 'gastroenterology',
                'name' => 'গ্যাস্ট্রোএন্টারোলজি ও লিভার (Gastroenterology)',
                'icon' => 'icofont-capsule',
                'keywords' => ['গ্যাস্ট্রো', 'লিভার', 'gastroenterology', 'hepatology', 'পরিপাকতন্ত্র', 'লিভার'],
            ],
            [
                'key' => 'endocrinology',
                'name' => 'ডায়াবেটিস ও হরমোন (Endocrinology)',
                'icon' => 'icofont-blood-drop',
                'keywords' => ['ডায়াবেটিস', 'ডায়াবেটিস', 'হরমোন', 'endocrinology', 'diabetes'],
            ],
            [
                'key' => 'pulmonology',
                'name' => 'বক্ষব্যাধি ও ফুসফুস (Pulmonology)',
                'icon' => 'icofont-lungs',
                'keywords' => ['ফুসফুস', 'বক্ষ', 'pulmonology', 'chest', 'শ্বাসতন্ত্র'],
            ],
            [
                'key' => 'psychiatry',
                'name' => 'মানসিক রোগ ও মনোরোগ (Psychiatry)',
                'icon' => 'icofont-brain-alt',
                'keywords' => ['মনোরোগ', 'মানসিক', 'psychiatry', 'psychiatric', 'কাউন্সেলিং'],
            ],
            [
                'key' => 'dental',
                'name' => 'ডেন্টাল ও মুখরোগ (Dental Care)',
                'icon' => 'icofont-tooth',
                'keywords' => ['ডেন্টাল', 'দন্ত', 'dental', 'মুখরোগ'],
            ],
            [
                'key' => 'ophthalmology',
                'name' => 'চক্ষু বিশেষজ্ঞ (Ophthalmology)',
                'icon' => 'icofont-eye',
                'keywords' => ['চক্ষু', 'চোখ', 'ophthalmology', 'eye'],
            ],
            [
                'key' => 'oncology',
                'name' => 'ক্যান্সার বিশেষজ্ঞ (Oncology)',
                'icon' => 'icofont-radiation',
                'keywords' => ['ক্যান্সার', 'oncology', 'টিউমার'],
            ],
            [
                'key' => 'hematology',
                'name' => 'রক্তরোগ বিশেষজ্ঞ (Hematology)',
                'icon' => 'icofont-blood',
                'keywords' => ['রক্ত', 'রক্তরোগ', 'hematology'],
            ],
            [
                'key' => 'physiotherapy',
                'name' => 'ফিজিওথেরাপি ও রিহ্যাব (Physiotherapy)',
                'icon' => 'icofont-wheelchair',
                'keywords' => ['ফিজিওথেরাপি', 'physiotherapy', 'rehab'],
            ],
            [
                'key' => 'radiology',
                'name' => 'রেডিওলজি ও ইমেজিং (Radiology & Imaging)',
                'icon' => 'icofont-xray',
                'keywords' => ['রেডিওলজি', 'আল্ট্রাসোনোগ্রাম', 'radiology', 'imaging', 'প্যাথলজি'],
            ],
            [
                'key' => 'nephrology',
                'name' => 'কিডনি ও নেফ্রোলজি (Nephrology)',
                'icon' => 'icofont-injection-syringe',
                'keywords' => ['নেফ্রোলজি', 'কিডনি', 'nephrology', 'ডায়ালাইসিস'],
            ],
            [
                'key' => 'nutrition',
                'name' => 'পুষ্টিবিদ ও ডায়েট (Nutrition & Dietetics)',
                'icon' => 'icofont-apple',
                'keywords' => ['পুষ্টি', 'ডায়েট', 'nutrition', 'diet', 'ডায়েটিশিয়ান'],
            ],
            [
                'key' => 'rheumatology',
                'name' => 'বাত ও রিউম্যাটোলজি (Rheumatology)',
                'icon' => 'icofont-bone',
                'keywords' => ['রিউম্যাটোলজি', 'বাত', 'rheumatology', 'গাঁটে ব্যথা'],
            ],
            [
                'key' => 'neurosurgery',
                'name' => 'নিউরোসার্জারি ও স্পাইন (Neurosurgery)',
                'icon' => 'icofont-brain',
                'keywords' => ['নিউরোসার্জারি', 'neurosurgery', 'স্পাইন', 'মেরুদণ্ড'],
            ],
            [
                'key' => 'cardiac_surgery',
                'name' => 'কার্ডিয়াক সার্জারি / হার্ট বাইপাস (Cardiac Surgery)',
                'icon' => 'icofont-heart-beat',
                'keywords' => ['কার্ডিয়াক সার্জারি', 'হার্ট সার্জারি', 'cardiac surgery', 'বাইপাস'],
            ],
            [
                'key' => 'pediatric_surgery',
                'name' => 'শিশু সার্জারি (Pediatric Surgery)',
                'icon' => 'icofont-baby',
                'keywords' => ['শিশু সার্জারি', 'pediatric surgery', 'বাচ্চাদের অপারেশন'],
            ],
            [
                'key' => 'plastic_surgery',
                'name' => 'প্লাস্টিক ও কসমেটিক সার্জারি (Plastic Surgery)',
                'icon' => 'icofont-cut',
                'keywords' => ['প্লাস্টিক সার্জারি', 'কসমেটিক', 'plastic surgery', 'cosmetic'],
            ],
        ];
    }

    /**
     * Get standard specializations enriched with real-time doctor counts (Cached).
     */
    public static function getStandardSpecializationsWithCounts(): array
    {
        return Cache::remember('std_spec_counts_v6', 3600, function () {
            $categories = self::getStandardSpecializations();
            $doctors = Doctor::approved()->select('id', 'specialization')->get();

            foreach ($categories as &$cat) {
                $keywords = $cat['keywords'];
                $pattern = '/'.implode('|', array_map('preg_quote', $keywords)).'/iu';
                $cat['count'] = $doctors->filter(function ($doc) use ($pattern) {
                    return ! empty($doc->specialization) && preg_match($pattern, $doc->specialization);
                })->count();
            }

            return $categories;
        });
    }
}
