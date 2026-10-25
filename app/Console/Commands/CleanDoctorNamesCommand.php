<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use Illuminate\Console\Command;

class CleanDoctorNamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doctors:purify-bangla {--id= : Purify single doctor by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean and purify all doctor names, designations and titles into 100% accurate, proper Bangla without spelling errors or duplicates';

    /**
     * Comprehensive Dictionary of Bangladeshi Names & Titles
     */
    public static array $dictionary = [
        // Titles & Honorifics
        'md' => 'মোঃ', 'mohammad' => 'মোহাম্মদ', 'mohammed' => 'মোহাম্মদ', 'muhammad' => 'মুহাম্মদ',
        'mst' => 'মোছাঃ', 'most' => 'মোছাঃ', 'engr' => 'প্রকৌঃ',
        'abu' => 'আবু', 'abdul' => 'আব্দুল', 'abdullah' => 'আব্দুল্লাহ', 'abul' => 'আবুল', 'al' => 'আল',
        'sheikh' => 'শেখ', 'kazi' => 'কাজী', 'quazi' => 'কাজী', 'mirza' => 'মির্জা', 'sardar' => 'সরদার',
        'bhuiyan' => 'ভূঁইয়া', 'munshi' => 'মুন্সী', 'khandaker' => 'খন্দকার', 'khondker' => 'খন্দকার', 'khondoker' => 'খন্দকার',
        'chowdhury' => 'চৌধুরী', 'choudhury' => 'চৌধুরী', 'chowdhuary' => 'চৌধুরী', 'chowdaury' => 'চৌধুরী',
        'majumder' => 'মজুমদার', 'mojumder' => 'মজুমদার', 'talukdar' => 'তালুকদার', 'talukder' => 'তালুকদার',
        'tarafdar' => 'তরফদার', 'sikder' => 'সিকদার', 'mollah' => 'মোল্লা', 'gazi' => 'গাজী', 'mia' => 'মিয়া', 'miah' => 'মিয়া',
        'dewan' => 'দেওয়ান', 'joarder' => 'জোয়ার্দার', 'howlader' => 'হাওলাদার',

        // Surnames & Family Names
        'ahmed' => 'আহমেদ', 'ahmad' => 'আহমদ', 'ahamed' => 'আহমেদ', 'ahamedur' => 'আহমেদুর', 'ahmedur' => 'আহমেদুর',
        'ali' => 'আলী', 'hasan' => 'হাসান', 'hassan' => 'হাসান',
        'hossain' => 'হোসেন', 'hussain' => 'হোসেন', 'hossen' => 'হোসেন',
        'rahman' => 'রহমান', 'rahaman' => 'রহমান', 'khan' => 'খান', 'islam' => 'ইসলাম',
        'begum' => 'বেগম', 'khatun' => 'খাতুন', 'akter' => 'আক্তার', 'akhter' => 'আক্তার', 'akther' => 'আক্তার',
        'nahar' => 'নাহার', 'sultana' => 'সুলতানা', 'jahan' => 'জাহান', 'parveen' => 'পারভীন', 'parvin' => 'পারভীন',
        'nasrin' => 'নাসরিন', 'sharmin' => 'শারমিন', 'yasmin' => 'ইয়াসমিন', 'fatema' => 'ফাতেমা', 'farhana' => 'ফারহানা',
        'haque' => 'হক', 'hoque' => 'হক', 'huq' => 'হক', 'haq' => 'হক',
        'uddin' => 'উদ্দীন', 'ullah' => 'উল্লাহ', 'zaman' => 'জামান', 'alam' => 'আলম',
        'karim' => 'করিম', 'rashid' => 'রশীদ', 'kamal' => 'কামাল',
        'mostafa' => 'মোস্তফা', 'mustafa' => 'মুস্তফা', 'mostofa' => 'মোস্তফা',
        'reza' => 'রেজা', 'shah' => 'শাহ', 'bari' => 'বারী', 'basher' => 'বশার', 'bashar' => 'বশার',
        'haider' => 'হায়দার', 'hayder' => 'হায়দার', 'haidar' => 'হায়দার', 'fazal' => 'ফজল',
        'siddique' => 'সিদ্দিকী', 'siddiqua' => 'সিদ্দিকা', 'siddiqa' => 'সিদ্দিকা', 'siddiki' => 'সিদ্দিকী', 'siddika' => 'সিদ্দিকা',
        'mallick' => 'মল্লিক', 'mollick' => 'মল্লিক',

        // Hindu Names
        'saha' => 'সাহা', 'ghosh' => 'ঘোষ', 'roy' => 'রায়', 'ray' => 'রায়', 'das' => 'দাস',
        'dutta' => 'দত্ত', 'datta' => 'দত্ত', 'sen' => 'সেন', 'barua' => 'বড়ুয়া', 'debnath' => 'দেবনাথ',
        'paul' => 'পাল', 'pal' => 'পাল', 'bhowmik' => 'ভৌমিক', 'biswas' => 'বিশ্বাস', 'bishwas' => 'বিশ্বাস',
        'banik' => 'বণিক', 'kundu' => 'কুন্ডু', 'chandra' => 'চন্দ্র', 'kumar' => 'কুমার',
        'adhikary' => 'অধিকারী', 'adhikari' => 'অধিকারী', 'majumdar' => 'মজুমদার', 'bhattacharjee' => 'ভট্টাচার্য',
        'chakraborty' => 'চক্রবর্তী', 'sarkar' => 'সরকার', 'sarker' => 'সরকার', 'karmakar' => 'কর্মকার', 'shil' => 'শীল',
        'mondal' => 'মন্ডল', 'kanti' => 'কান্তি', 'rani' => 'রানী',

        // Common First & Middle Names (A-Z)
        'ara' => 'আরা', 'banu' => 'বানু', 'nobi' => 'নবী', 'nabi' => 'নবী', 'razzak' => 'রাজ্জাক',
        'kamruzzaman' => 'কামরুজ্জামান', 'sharifuzzaman' => 'শরিফুজ্জামান', 'rezaul' => 'রেজাউল',
        'mahmudul' => 'মাহমুদুল', 'rashedul' => 'রাশেদুল', 'saleh' => 'সালেহ', 'panna' => 'পান্না',
        'indrajit' => 'ইন্দ্রজিৎ', 'indrojit' => 'ইন্দ্রজিৎ', 'ismat' => 'ইসমাত', 'arefin' => 'আরেফিন',
        'morsalin' => 'মোরসালিন', 'sirajul' => 'সিরাজুল', 'shirajul' => 'সিরাজুল', 'man' => 'মান',
        'shirin' => 'শিরিন', 'afsar' => 'আফসার', 'benozir' => 'বেনজির',
        'sadia' => 'সাদিয়া', 'jesmin' => 'জেসমিন', 'ilias' => 'ইলিয়াস', 'firoj' => 'ফিরোজ',
        'ibna' => 'ইবনে', 'ibne' => 'ইবনে', 'ahad' => 'আহাদ', 'tusher' => 'তুষার', 'tushar' => 'তুষার',
        'areju' => 'আরেজু', 'taskina' => 'তাসকিনা', 'azizul' => 'আজিজুল', 'mosharof' => 'মোশাররফ',
        'mosharraf' => 'মোশাররফ', 'shimul' => 'শিমুল', 'hazera' => 'হাজেরা', 'zamil' => 'জামিল',
        'zaidur' => 'জাইদুর', 'rahim' => 'রহিম', 'hasinatul' => 'হাসিনাতুল', 'zannat' => 'জান্নাত',
        'jannat' => 'জান্নাত', 'jannatul' => 'জান্নাতুল', 'zannatul' => 'জান্নাতুল',
        'mazharul' => 'মাযহারুল', 'tanvir' => 'তানভীর', 'sarwar' => 'সারওয়ার', 'ferdaus' => 'ফেরদৌস',
        'ferdous' => 'ফেরদৌস', 'kafi' => 'কাফি', 'khurshid' => 'খুরশীদ', 'khurshed' => 'খুরশেদ', 'khursed' => 'খুরশেদ',
        'shefin' => 'শেফিন', 'masuda' => 'মাসুদা', 'baqui' => 'বাকী', 'shaheed' => 'শহীদ', 'shahid' => 'শহীদ',
        'nurannabi' => 'নূরুন্নবী', 'ranu' => 'রানু', 'ruma' => 'রুমা', 'laila' => 'লায়লা',
        'salma' => 'সালমা', 'rokeya' => 'রোকেয়া', 'monir' => 'মনির', 'moniruzzaman' => 'মনিরুজ্জামান',
        'ashraf' => 'আশরাফ', 'ashraful' => 'আশরাফুল', 'anisur' => 'আনিসুর', 'anis' => 'আনিস',
        'motiur' => 'মতিউর', 'mizanur' => 'মিজানুর', 'lutfor' => 'লুৎফর', 'lutfur' => 'লুৎফর',
        'sayeed' => 'সাঈদ', 'saeed' => 'সাঈদ', 'shahidul' => 'শহীদুল', 'nazrul' => 'নজরুল',
        'enamul' => 'এনামুল', 'ziaur' => 'জিয়াউর', 'habibur' => 'হাবিবুর', 'saiful' => 'সাইফুল',
        'tariqul' => 'তরিকুল', 'zahid' => 'জাহিদ', 'zahidul' => 'জাহিদুল', 'rashed' => 'রাশেদ',
        'faisal' => 'ফয়সাল', 'imran' => 'ইমরান', 'arif' => 'আরিফ', 'ariful' => 'আরিফুল',
        'tareq' => 'তারেক', 'shohel' => 'সোহেল', 'sumon' => 'সুমন', 'shakil' => 'শাকিল',
        'rubel' => 'রুবেল', 'ripon' => 'রিপন', 'shuvo' => 'শুভ', 'sabbir' => 'সাব্বির',
        'nayeem' => 'নাঈম', 'tamim' => 'তামিম', 'rifat' => 'রিফাত', 'siam' => 'সিয়াম',
        'arafat' => 'আরাফাত', 'riaz' => 'রিয়াজ', 'anwar' => 'আনোয়ার', 'golam' => 'গোলাম',
        'shamsul' => 'শামসুল', 'fazlul' => 'ফজলুল', 'asif' => 'আসিফ', 'adnan' => 'আদনান',
        'shahin' => 'শাহীন', 'sohag' => 'সোহাগ', 'mehedi' => 'মেহেদী', 'nazmul' => 'নাজমুল',
        'ashik' => 'আশিক', 'sakib' => 'সাকিব', 'noman' => 'নোমান', 'helal' => 'হেলাল',
        'habib' => 'হাবিব', 'harun' => 'হারুন', 'selim' => 'সেলিম', 'salim' => 'সেলিম',
        'mamun' => 'মামুন', 'monjur' => 'মঞ্জুর', 'manzur' => 'মনজুর', 'jahangir' => 'জাহাঙ্গীর',
        'alamgir' => 'আলমগীর', 'jahidul' => 'জাহিদুল', 'zakir' => 'জাকির', 'zobaida' => 'জুবাইদা',
        'tasnim' => 'তাসনিম', 'tasneem' => 'তাসনীম', 'tanzila' => 'তানজিলা', 'tanjina' => 'তানজিনা',
        'sabrina' => 'সাবরিনা', 'nadia' => 'নাদিয়া', 'nusrat' => 'নুসরাত', 'nargis' => 'নার্গিস',
        'samina' => 'সামিনা', 'shahana' => 'শাহানা', 'rebeka' => 'রেবেকা', 'afroza' => 'আফরোজা',
        'tahmina' => 'তাহমিনা', 'tahsina' => 'তাহসিনা', 'tahasina' => 'তাহসিনা', 'tahsin' => 'তাহসিন',
        'shahnaz' => 'শাহনাজ', 'bilkis' => 'বিলকিস', 'suraiya' => 'সুরাইয়া', 'mahmud' => 'মাহমুদ',
        'mahmood' => 'মাহমুদ', 'kabir' => 'কবির', 'amin' => 'আমিন', 'aminul' => 'আমিনুল',
        'khalid' => 'খালিদ', 'saif' => 'সাইফ', 'sohel' => 'সোহেল', 'hasnat' => 'হাসনাত',
        'badal' => 'বাদল', 'tariq' => 'তারেক', 'munir' => 'মুনীর', 'ashfaq' => 'আশফাক',
        'quddus' => 'কুদ্দুস', 'quader' => 'কাদের', 'kader' => 'কাদের', 'jalil' => 'জলিল',
        'khalil' => 'খলিল', 'latif' => 'লতিফ', 'rouf' => 'রউফ', 'wahab' => 'ওয়াহাব',
        'baset' => 'বাসেত', 'samad' => 'সামাদ', 'mannan' => 'মান্নান', 'hannan' => 'হান্নান',
        'jabbar' => 'জব্বার', 'sattar' => 'সাত্তার', 'gaffar' => 'গফফার', 'shafi' => 'শফি',
        'shafiq' => 'শফিক', 'shafiqul' => 'শফিকুল', 'rafiq' => 'রফিক', 'rafiqul' => 'রফিকুল',
        'taufiq' => 'তৌফিক', 'toufiq' => 'তৌফিক', 'firoz' => 'ফিরোজ', 'feroz' => 'ফিরোজ',
        'hafiz' => 'হাফিজ', 'hafizur' => 'হাফিজুর', 'farid' => 'ফরিদ', 'faruk' => 'ফারুক',
        'farooq' => 'ফারুক', 'farhad' => 'ফরহাদ', 'forhad' => 'ফরহাদ', 'anwarul' => 'আনোয়ারুল',
        'azhar' => 'আজহার', 'akram' => 'আকরাম', 'aslam' => 'আসলাম', 'azad' => 'আজাদ',
        'helaluddin' => 'হেলাল উদ্দীন', 'nizam' => 'নিজাম', 'masum' => 'মাসুম', 'kaniz' => 'কানিজ',
        'ferdousi' => 'ফেরদৌসী', 'kamrun' => 'কামরুন', 'badrun' => 'বদরুন', 'khurshida' => 'খুরশীদা',
        'rowshan' => 'রওশন', 'rowshon' => 'রওশন', 'shamsun' => 'শামসুন', 'nurjahan' => 'নূরজাহান', 'hurjahan' => 'হুরজাহান', 'shahida' => 'শাহিদা',
        'monowara' => 'মনোয়ারা', 'lutfa' => 'লুৎফা', 'parul' => 'পারুল', 'sheuly' => 'শিউলি',
        'shikha' => 'শিখা', 'rekha' => 'রেখা', 'snigdha' => 'স্নিগ্ধা', 'dipti' => 'দীপ্তি',
        'dalia' => 'ডালিয়া', 'niloufar' => 'নিলুফার', 'nilufar' => 'নিলুফার', 'samiul' => 'সামিউল',
        'sydur' => 'সাইদুর', 'saidur' => 'সাইদুর', 'mahtabur' => 'মাহতাবুর', 'mostafizur' => 'মোস্তাফিজুর',
        'bakhtiar' => 'বখতিয়ার', 'azam' => 'আজম', 'antara' => 'অন্তরা', 'shuvra' => 'শুভ্রা',
        'subhra' => 'শুভ্রা', 'talibul' => 'তালিবুল', 'mou' => 'মৌ', 'urmi' => 'উর্মি',
        'musarrat' => 'মুসাররাত', 'delwar' => 'দেলোয়ার', 'delowar' => 'দেলোয়ার', 'showkat' => 'শওকত',
        'shawkat' => 'শওকত', 'zahiruddin' => 'জহিরুদ্দীন', 'saifuddin' => 'সাইফুদ্দিন',
        'shamsuddin' => 'শামসুদ্দীন', 'sufia' => 'সুফিয়া', 'mahfuza' => 'মাহফুজা', 'naima' => 'নাইমা',
        'shameem' => 'শামীম', 'afza' => 'আফজা', 'maruf' => 'মারুফ', 'mumin' => 'মুমিন',
        'mithila' => 'মিথিলা', 'humayun' => 'হুমায়ুন', 'shahan' => 'শাহান', 'rusho' => 'রুশো',
        'sumi' => 'সুমি', 'atiqul' => 'আতিকুল', 'atiquel' => 'আতিকুল', 'atiqur' => 'আতিকুর',
        'atiquzzaman' => 'আতিকুজ্জামান', 'akhtar' => 'আখতার', 'akhtaruzzaman' => 'আখতারুজ্জামান',
        'raquib' => 'রাকিব', 'qamrul' => 'কামরুল', 'quamrul' => 'কামরুল', 'kamrul' => 'কামরুল',
        'iqbal' => 'ইকবাল', 'quyyum' => 'কাইয়ুম', 'quayum' => 'কাইয়ুম', 'rafiquzzaman' => 'রফিকুজ্জামান',
        'qasem' => 'কাশেম', 'isteaq' => 'ইশতিয়াক', 'ashiqur' => 'আশিকুর', 'faruque' => 'ফারুক',
        'anica' => 'আনিকা', 'mostaque' => 'মোস্তাক', 'tasmin' => 'তাসমিন', 'yazdi' => 'ইয়াজদি',
        'mamtaz' => 'মমতাজ', 'sayat' => 'সায়াত', 'sanju' => 'সঞ্জু', 'moon' => 'মুন',
        'shaon' => 'শাওন', 'fuad' => 'ফুয়াদ', 'krishna' => 'কৃষ্ণ', 'nawazes' => 'নওয়াজেস',
        'rajibur' => 'রাজিবুর', 'fahad' => 'ফাহাদ', 'fouzia' => 'ফৌজিয়া',
        'tiasha' => 'তিয়াশা', 'niaz' => 'নিয়াজ', 'anam' => 'আনাম', 'nilav' => 'নীলাভ',
        'milton' => 'মিল্টন', 'newton' => 'নিউটন', 'niamul' => 'নিয়ামুল', 'neamul' => 'নিয়ামুল',
        'goni' => 'গনি', 'ghani' => 'গনি', 'zinnurain' => 'জিন্নুরাইন', 'zinnu' => 'জিন্নু', 'rain' => 'রাইন',
        'binte' => 'বিন্তে', 'bint' => 'বিন্তে', 'aisha' => 'আয়েশা', 'ayesha' => 'আয়েশা',
        'tajmira' => 'তাজমিরা', 'tazmeri' => 'তাজমেরি', 'tazmira' => 'তাজমিরা', 'mustary' => 'মুস্তারি',
        'tayob' => 'তৈয়ব', 'taiyeb' => 'তৈয়ব', 'tayeb' => 'তৈয়ব', 'faryal' => 'ফারিয়াল',
        'nawaz' => 'নেওয়াজ', 'newaz' => 'নেওয়াজ', 'farzana' => 'ফারজানা', 'noor' => 'নূর', 'nur' => 'নূর',
        'ahsan' => 'আহসান', 'nurul' => 'নুরুল', 'shamim' => 'শামীম', 'abdur' => 'আবদুর',
        'mahbub' => 'মাহবুব', 'morshed' => 'মোরশেদ', 'masud' => 'মাসুদ', 'kalam' => 'কালাম',
        'fahmida' => 'ফাহমিদা', 'rana' => 'রানা', 'abdus' => 'আবদুস', 'nahid' => 'নাহিদ',
        'sharif' => 'শরিফ', 'ruhul' => 'রুহুল', 'shariful' => 'শরিফুল', 'hasina' => 'হাসিনা',
        'shamima' => 'শামীমা', 'afroz' => 'আফরোজ', 'ibrahim' => 'ইব্রাহিম', 'aziz' => 'আজিজ',
        'mahbubul' => 'মাহবুবুল', 'omar' => 'ওমর', 'asaduzzaman' => 'আসাদুজ্জামান', 'salam' => 'সালাম',
        'iftekhar' => 'ইফতেখার', 'mahbubur' => 'মাহবুবুর', 'nasir' => 'নাসির', 'naznin' => 'নাজনীন',
        'shahadat' => 'শাহাদাত', 'jewel' => 'জুয়েল', 'tamanna' => 'তামান্না', 'jamal' => 'জামাল',
        'afsana' => 'আফসানা', 'imtiaz' => 'ইমতিয়াজ', 'shahriar' => 'শাহরিয়ার', 'sabina' => 'সাবিনা',
        'ishrat' => 'ইশরাত', 'akbar' => 'আকবর', 'afrin' => 'আফরিন', 'umme' => 'উম্মে',
        'khairul' => 'খায়রুল', 'mohiuddin' => 'মহিউদ্দীন', 'hena' => 'হেনা',
        'iffat' => 'ইফফাত', 'subrata' => 'সুব্রত', 'swapan' => 'স্বপন', 'mukul' => 'মুকুল',
        'dulal' => 'দুলাল', 'tapan' => 'তপন', 'chandan' => 'চন্দন', 'sujit' => 'সুজিৎ',
        'sanjib' => 'সঞ্জীব', 'rajib' => 'রাজীব', 'sajib' => 'সজীব', 'pradip' => 'প্রদীপ',
        'goutam' => 'গৌতম', 'uttam' => 'উত্তম', 'kajal' => 'কাজল', 'kazal' => 'কাজল',
        'tapash' => 'তাপস', 'tapas' => 'তাপস', 'ananya' => 'অনন্যা', 'shanta' => 'শান্তা',
        'shanto' => 'শান্ত', 'ananta' => 'অনন্ত', 'tanu' => 'তনু', 'tania' => 'তানিয়া',
        'sonia' => 'সোনিয়া', 'mousumi' => 'মৌসুমী', 'mowsumi' => 'মৌসুমী', 'poly' => 'পলি',
        'doly' => 'ডলি', 'popy' => 'পপি', 'moly' => 'মলি', 'shoma' => 'সোমা', 'jhuma' => 'ঝুমা',
        'roma' => 'রোমা', 'lata' => 'লতা', 'ujjal' => 'উজ্জ্বল', 'ujjwal' => 'উজ্জ্বল',
        'awwal' => 'আউয়াল', 'awal' => 'আউয়াল', 'sarower' => 'সারোয়ার', 'sarwer' => 'সারওয়ার',
        'shammi' => 'শাম্মী', 'cotton' => 'কটন', 'daise' => 'ডেইজি', 'sunvee' => 'সানভী',
        // Additional Bangladeshi names and edge cases
        'mosheeur' => 'মশিউর', 'moshiur' => 'মশিউর', 'mashiur' => 'মশিউর',
        'gias' => 'গিয়াস', 'neaz' => 'নিয়াজ', 'atiar' => 'আতিয়ার', 'matiar' => 'মতিয়ার',
        'tauhidul' => 'তৌহিদুল', 'touhidul' => 'তৌহিদুল', 'tauhid' => 'তৌহিদ', 'touhid' => 'তৌহিদ',
        'towhid' => 'তৌহিদ', 'towhidul' => 'তৌহিদুল',
        'salauddin' => 'সালাহউদ্দীন', 'alauddin' => 'আলাউদ্দীন', 'ziauddin' => 'জিয়াউদ্দীন',
        'ulubbi' => 'উলুব্বী', 'moazzem' => 'মোয়াজ্জেম', 'moazzam' => 'মোয়াজ্জেম',
        'tarique' => 'তারেক', 'tareque' => 'তারেক', 'atique' => 'আতিক', 'sadeque' => 'সাদেক',
        'ronee' => 'রনি', 'rony' => 'রনি', 'ganesh' => 'গণেশ',
        'saurav' => 'সৌরভ', 'sourav' => 'সৌরভ', 'shourav' => 'সৌরভ',
        'iktier' => 'ইখতিয়ার', 'ikhtiar' => 'ইখতিয়ার',
        'kauser' => 'কাউসার', 'kausar' => 'কাউসার', 'kawsar' => 'কাওসার', 'kawser' => 'কাওসার',
        'shouaibe' => 'শুয়াইব', 'shuaib' => 'শুয়াইব', 'fahdia' => 'ফাহদিয়া',
        'motiul' => 'মতিউল', 'reaz' => 'রিয়াজ', 'reazul' => 'রিয়াজুল',
        'kibria' => 'কিবরিয়া', 'zakaria' => 'জাকারিয়া', 'robiul' => 'রবিউল', 'rabiul' => 'রবিউল',
        'yeakub' => 'ইয়াকুব', 'yakub' => 'ইয়াকুব', 'yeasmin' => 'ইয়াসমিন', 'sohael' => 'সোহেল', 'husein' => 'হোসেন',
        'baowaly' => 'বাওয়ালী', 'meaji' => 'মিয়াজী', 'sujon' => 'সুজন', 'mushiar' => 'মুশিয়ার',
        'waliul' => 'ওয়ালীউল', 'leea' => 'লিয়া', 'ifrad' => 'ইফরাদ', 'raunak' => 'রওনক',
        'ania' => 'আনিয়া', 'atia' => 'আতিয়া', 'olia' => 'ওলিয়া', 'nighat' => 'নিগহাত',
        'rooh' => 'রূহ', 'mosiul' => 'মসিউল', 'zafar' => 'জাফর', 'siddhartha' => 'সিদ্ধার্থ',
        'joardar' => 'জোয়ার্দার', 'niatuzzaman' => 'নিয়তুজ্জামান', 'mazibur' => 'মজিবুর',
        'matiur' => 'মতিউর', 'shafiur' => 'শফিউর', 'shafiul' => 'শফিউল', 'shamiul' => 'শামিউল',
        'raziur' => 'রাজিউর', 'rafiul' => 'রাফিউল', 'ziaul' => 'জিয়াউল', 'liaquat' => 'লিয়াকত',
        'ishtiaq' => 'ইশতিয়াক', 'istiak' => 'ইশতিয়াক', 'beauty' => 'বিউটি', 'tofael' => 'তোফায়েল',
        'shiam' => 'সিয়াম', 'zakariah' => 'জাকারিয়া', 'prianka' => 'প্রিয়াঙ্কা', 'shariar' => 'শাহরিয়ার',
        'foez' => 'ফয়েজ', 'yeusuf' => 'ইউসুফ', 'yusuf' => 'ইউসুফ', 'moeen' => 'মঈন',
        'quayyum' => 'কাইয়ুম', 'elias' => 'ইলিয়াস', 'sorroare' => 'সারোয়ার', 'soroar' => 'সারোয়ার',
        'shoaib' => 'শোয়েব', 'hosnea' => 'হোসনে', 'bhuiya' => 'ভূঁইয়া', 'shiek' => 'শেখ',
        'ziasmin' => 'জেসমিন', 'ikhtear' => 'ইখতিয়ার', 'shahreor' => 'শাহরিয়ার', 'razzaque' => 'রাজ্জাক',
        'muqueet' => 'মুকিত', 'quadir' => 'কাদের', 'muizzul' => 'মুইজুল', 'daluar' => 'দেলোয়ার',
        'mosiur' => 'মশিউর', 'fouad' => 'ফুয়াদ', 'meah' => 'মিয়া', 'annie' => 'অ্যানি',
        'tonima' => 'তনিমা', 'quanak' => 'কনক', 'jobira' => 'জোবাইরা', 'jeanat' => 'জান্নাত',
        'rokiur' => 'রকিবুর', 'rokib' => 'রকিব', 'seirina' => 'সেরিনা', 'zabiul' => 'জাবিউল',
        'raaisul' => 'রাইসুল', 'shaafi' => 'শাফী', 'munthasir' => 'মুনতাসির', 'munmun' => 'মুনমুন',
        'hossaain' => 'হোসেন', 'mohamed' => 'মোহাম্মদ', 'joan' => 'জোয়ান', 'hasanat' => 'হাসানাত',
        'chhonda' => 'ছন্দা', 'illius' => 'ইলিয়াস', 'amatullah' => 'আমাতুল্লাহ', 'sharmeen' => 'শারমিন',
        'moinuddin' => 'মঈনউদ্দীন',
        'rakhi' => 'রাখী', 'debi' => 'দেবী', 'shohael' => 'সোহেল', 'monoarul' => 'মনোয়ারুল',
        'niamat' => 'নেয়ামত', 'alahi' => 'এলাহী', 'elahi' => 'এলাহী', 'rabea' => 'রাবেয়া',
        'riad' => 'রিয়াদ', 'reazuddin' => 'রিয়াজউদ্দীন', 'neon' => 'নিয়ন',
        'mustaquim' => 'মুস্তাকিম', 'farooqui' => 'ফারুকী', 'sufian' => 'সুফিয়ান',
        'liakat' => 'লিয়াকত', 'imtiaj' => 'ইমতিয়াজ', 'raquiba' => 'রকিবা',
        'iftequar' => 'ইফতেখার', 'foara' => 'ফোয়ারা', 'rafiat' => 'রাফিয়াত',
        'yeashmin' => 'ইয়াসমিন', 'siddiquey' => 'সিদ্দিকী',
        'sarke' => 'সরকার', 'tarequr' => 'তারেকুর', 'rizwan' => 'রিজওয়ান',
        'redwan' => 'রেদোয়ান', 'redowan' => 'রেদোয়ান', 'wadud' => 'ওয়াদুদ',
        'russel' => 'রাসেল', 'tarun' => 'তরুণ', 'ranjan' => 'রঞ্জন',
        'shushanta' => 'সুশান্ত', 'somita' => 'সুমিতা',
        'juwel' => 'জুয়েল', 'dawan' => 'দেওয়ান', 'alom' => 'আলম',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=========================================================');
        $this->info(' Purifying All Doctor Names & Designations into Pure Bangla');
        $this->info('=========================================================');

        $query = Doctor::query();
        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        $doctors = $query->get();
        $total = $doctors->count();
        $this->info("Processing {$total} doctors in database...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updatedCount = 0;

        foreach ($doctors as $doctor) {
            $oldName = $doctor->name;
            $slug = $doctor->slug;

            // Only normalize imported doctors or doctors with anomalies
            $isImported = (stripos($slug, 'ibnsina') !== false || stripos($slug, 'popular') !== false || $doctor->id > 567);
            $hasAnomaly = (preg_match('/[a-zA-Z]/', $oldName) || self::hasDuplicationOrAnomaly($oldName));

            if ($isImported || $hasAnomaly) {
                $pureName = self::normalizeDoctorName($slug, $oldName);
            } else {
                // Handcrafted seed doctors - clean duplicate halves if any
                $pureName = self::cleanDuplicatedHalves($oldName);
            }

            $pureDesignation = self::toPureBanglaDesignation($doctor->designation);

            // Clean bio of English in brackets or mangled doctor names
            $pureBio = $doctor->bio;
            if ($pureBio) {
                $pureBio = preg_replace("/\([^)]*[a-zA-Z]{3,}[^)]*\)/u", '', $pureBio);
                if ($oldName !== $pureName) {
                    $pureBio = str_replace($oldName, $pureName, $pureBio);
                }
                $pureBio = preg_replace("/\s+/", ' ', trim($pureBio));
            }

            if ($doctor->name !== $pureName || $doctor->designation !== $pureDesignation || $doctor->bio !== $pureBio) {
                $doctor->name = $pureName;
                $doctor->designation = $pureDesignation;
                $doctor->bio = $pureBio;
                $doctor->save();
                $updatedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=========================================================');
        $this->info(" Successfully Purified {$updatedCount} of {$total} Doctors into 100% Accurate Bangla!");
        $this->info('=========================================================');

        return 0;
    }

    /**
     * Check if a name contains repeated halves or corrupted characters
     */
    public static function hasDuplicationOrAnomaly(string $name): bool
    {
        if (preg_match('/[a-zA-Z]/', $name)) {
            return true;
        }
        if (preg_match('/(?:দদি|জাননাট|মুসাররাট|চোওধ|এ\.আর\.এ\.|এম\.ও\.ইউ\.|রোওশ|দালিা|সয়দুর|ইলিাস|ফোরহাদ|সিদদি|পাননা|ইসমাট|নোবি|তাহmina|আসসটট|উজজাল|আওওাল|সারোওের|চোওদাুরয়|থুরসদায়|মর্নিং|ইভনিং|রািখি|সেনিোর)/u', $name)) {
            return true;
        }

        $words = preg_split('/\s+/u', trim($name));
        $cnt = count($words);
        if ($cnt >= 4 && $cnt % 2 === 0) {
            $h = $cnt / 2;
            if (array_slice($words, 0, $h) === array_slice($words, $h)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clean duplicated halves from a name
     */
    public static function cleanDuplicatedHalves(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name));
        $cnt = count($words);
        if ($cnt >= 4 && $cnt % 2 === 0) {
            $h = $cnt / 2;
            $first = array_slice($words, 0, $h);
            $second = array_slice($words, $h);
            if ($first === $second) {
                return implode(' ', $first);
            }
        }

        return $name;
    }

    /**
     * Reconstruct pure, correctly spelled Bengali doctor name from slug
     */
    public static function normalizeDoctorName(string $slug, string $existingName = ''): string
    {
        // 1. Strip hospital suffix
        $cleanSlug = preg_replace('/-(?:ibnsina|popular)-\d+$/', '', $slug);

        // Strip shift and day names
        $cleanSlug = preg_replace('/-(?:morning|evening|night|afternoon|saturday|sunday|monday|tuesday|wednesday|thursday|friday)(?=-|$)/i', '', $cleanSlug);

        // Strip professions & roles from slug (at end only)
        $cleanSlug = preg_replace('/-(?:cardiologist|nutritionist|physiotherapist|physio|specialist)$/i', '', $cleanSlug);
        $cleanSlug = preg_replace('/^(?:nutritionistummay|nutritionist|physio)-?/i', '', $cleanSlug);

        // Strip trailing retired/retd/lpr/spp/lrp
        $cleanSlug = preg_replace('/-(?:retired|retd|rtd|lpr|spp|lrp)$/i', '', $cleanSlug);
        $cleanSlug = preg_replace('/-(?:spp|lrp)$/i', '', $cleanSlug);

        // Strip attached lpr (e.g. chowdhurylpr -> chowdhury)
        $cleanSlug = preg_replace('/(?<=[a-z])lpr\b/i', '', $cleanSlug);

        // 2. Detect title & military prefixes from slug
        $isMilitary = '';
        if (preg_match('/(?:brigadier-general|brigadiar-general|brig-gen|briggen|brigadier)/i', $cleanSlug)) {
            $isMilitary = 'ব্রিগেডিয়ার জেনারেল (অবঃ)';
        } elseif (preg_match('/\b(?:lieutenant-colonel|lt-col|lieutenant|lt)\b/i', $cleanSlug)) {
            $isMilitary = 'লেফটেন্যান্ট কর্নেল (অবঃ)';
        } elseif (preg_match('/\b(?:colonel|col)\b/i', $cleanSlug)) {
            $isMilitary = 'কর্নেল (অবঃ)';
        } elseif (preg_match('/\b(?:major|maj)\b/i', $cleanSlug)) {
            $isMilitary = 'মেজর (অবঃ)';
        }

        $isProf = (bool) preg_match('/(?:professor|prof)/i', $cleanSlug);
        $isAssoc = (bool) preg_match('/(?:associate-professor|associate-prof|associateprofessor|associateprof|assoc-professor|assoc-prof|assocprofessor|assocprof|asso-professor|asso-prof|assoprofessor|assoprof|assoprodr|assotprofessor|assotprof|associate|assoc|asso)/i', $cleanSlug);
        $isAsst = (bool) preg_match('/(?:assistant-professor|assistant-prof|assistantprofessor|assistantprof|assist-professor|assist-prof|assistprofessor|assistprof|assist-prodr|assistprodr|asstt-professor|asstt-prof|assttprofessor|assttprof|asst-professor|asst-prof|asstprofessor|asstprof|asstprodr|asist-prof|assis-prof|assit-prof|assot-prof|assistant|asstt|asst)/i', $cleanSlug);
        $isSeniorConsultant = (bool) preg_match('/(?:senior-consultant|senior)/i', $cleanSlug);

        // Strip military prefixes
        $cleanSlug = preg_replace('/^(?:brigadier-general|brigadiar-general|brig-gen|briggen|lieutenant-colonel|lt-col|colonel|major)(?:-?prof(?:essor)?(?:-?dr)?)?-?/i', '', $cleanSlug);

        // Strip senior consultant
        $cleanSlug = preg_replace('/^(?:senior-consultant-dr|senior-consultant|senior-dr|senior)-?/i', '', $cleanSlug);

        // Strip assistant / associate / professor prefixes including variations like assist-prodr, asstprodr, assoprodr
        $cleanSlug = preg_replace('/^(?:assoprodr|assoprofdr|assopro|assotpro|asstprodr|assist-prodr|assistprodr|assistant-professor|assistant-prof|assistantprofessor|assistantprof|assist-professor|assist-prof|assistprofessor|assistprof|asstt-professor|asstt-prof|assttprofessor|assttprof|asst-professor|asst-prof|asstprofessor|asstprof|asist-prof|assis-prof|assit-prof|assot-prof|assotprofessor|associate-professor|associate-prof|associateprofessor|associateprof|assoc-professor|assoc-prof|assocprofessor|assocprof|asso-professor|asso-prof|assoprofessor|assoprof|professor|prof)(?:-?dr(?:prof)?|-?prodr)?-?/i', '', $cleanSlug);

        // Strip remaining isolated dr/prodr tokens
        $cleanSlug = preg_replace('/^(?:dr|prodr|doctor)-?/i', '', $cleanSlug);
        $cleanSlug = preg_replace('/-(?:dr|prodr)(?=[a-z]{3,})/i', '-', $cleanSlug);

        // Handle attached md or sm: mdmashikul -> md-mashikul, mmamun -> md-mamun
        $cleanSlug = preg_replace('/^(?:sm|md)(?=[a-z]{3,})/i', '$0-', $cleanSlug);
        $cleanSlug = preg_replace('/-(?:sm|md)(?=[a-z]{3,})/i', '-$0-', $cleanSlug);
        $cleanSlug = preg_replace('/^m(?=mamun)/i', 'md-', $cleanSlug);

        // Strip prefix tokens from slug
        $cleanSlug = preg_replace('/\b(?:brigadier-general|brigadiar-general|brig-gen|briggenr|briggen|brigadier|brig|lieutenant-colonel|lt-col|lieutenant|lt|colonel|col|major|maj|assistant-prof|asst-prof|asstt-prof|asist-prof|assis-prof|assit-prof|assot-prof|associate-prof|assoc-prof|asso-prof|professor|prof|assistant|associate|asstt|asst|assoc|asso|dr|prodr|profdr|asst-profdrmd|retd|rtd|lpr|spp|lrp|senior-consultant|unit-head)\b-?/i', '', $cleanSlug);

        $cleanSlug = trim($cleanSlug, '-');

        // Determine title prefix
        if ($isMilitary && $isProf) {
            $prefix = $isMilitary.' অধ্যাপক ডাঃ ';
        } elseif ($isMilitary) {
            $prefix = $isMilitary.' ডাঃ ';
        } elseif ($isAsst) {
            $prefix = 'সহকারী অধ্যাপক ডাঃ ';
        } elseif ($isAssoc) {
            $prefix = 'সহযোগী অধ্যাপক ডাঃ ';
        } elseif ($isProf) {
            $prefix = 'অধ্যাপক ডাঃ ';
        } elseif ($isSeniorConsultant) {
            $prefix = 'সিনিয়র কনসালট্যান্ট ডাঃ ';
        } else {
            $prefix = 'ডাঃ ';
        }

        // Preserve academic rank from existing name if present
        if (mb_strpos($existingName, 'সহকারী অধ্যাপক') !== false && ! $isAsst && ! $isAssoc && ! $isProf) {
            $prefix = 'সহকারী অধ্যাপক ডাঃ ';
        } elseif (mb_strpos($existingName, 'সহযোগী অধ্যাপক') !== false && ! $isAsst && ! $isAssoc && ! $isProf) {
            $prefix = 'সহযোগী অধ্যাপক ডাঃ ';
        } elseif (mb_strpos($existingName, 'অধ্যাপক') !== false && ! $isAsst && ! $isAssoc && ! $isProf) {
            $prefix = 'অধ্যাপক ডাঃ ';
        }

        $parts = explode('-', $cleanSlug);
        $bnTokens = [];

        foreach ($parts as $p) {
            $p = strtolower(trim($p));
            if (! $p || is_numeric($p)) {
                continue;
            }
            if (in_array($p, ['evening', 'morning', 'night', 'afternoon', 'spec', 'dr', 'doctor', 'retired', 'retd', 'lpr', 'saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
                continue;
            }

            if (isset(self::$dictionary[$p])) {
                $bnTokens[] = self::$dictionary[$p];
            } elseif (strlen($p) === 1) {
                // Single letter initial
                $letterMap = [
                    'a' => 'এ.', 'b' => 'বি.', 'c' => 'সি.', 'd' => 'ডি.', 'e' => 'ই.', 'f' => 'এফ.',
                    'g' => 'জি.', 'h' => 'এইচ.', 'i' => 'আই.', 'j' => 'জে.', 'k' => 'কে.', 'l' => 'এল.',
                    'm' => 'এম.', 'n' => 'এন.', 'o' => 'ও.', 'p' => 'পি.', 'q' => 'কিউ.', 'r' => 'আর.',
                    's' => 'এস.', 't' => 'টি.', 'u' => 'ইউ.', 'v' => 'ভি.', 'w' => 'ডব্লিউ.', 'x' => 'এক্স.',
                    'y' => 'ওয়াই.', 'z' => 'জেড.',
                ];
                $bnTokens[] = $letterMap[$p] ?? $p;
            } elseif (strlen($p) <= 3 && ctype_alpha($p)) {
                // Multi-letter initials like akm, abm, fh, sk, sm
                $letterMap = [
                    'a' => 'এ', 'b' => 'বি', 'c' => 'সি', 'd' => 'ডি', 'e' => 'ই', 'f' => 'এফ',
                    'g' => 'জি', 'h' => 'এইচ', 'i' => 'আই', 'j' => 'জে', 'k' => 'কে', 'l' => 'এল',
                    'm' => 'এম', 'n' => 'এন', 'o' => 'ও', 'p' => 'পি', 'q' => 'কিউ', 'r' => 'আর',
                    's' => 'এস', 't' => 'টি', 'u' => 'ইউ', 'v' => 'ভি', 'w' => 'ডব্লিউ', 'x' => 'এক্স',
                    'y' => 'ওয়াই', 'z' => 'জেড',
                ];
                $sub = [];
                for ($k = 0; $k < strlen($p); $k++) {
                    $sub[] = $letterMap[$p[$k]] ?? $p[$k];
                }
                $bnTokens[] = implode('. ', $sub).'.';
            } else {
                $bnTokens[] = self::transliteratePhonetic($p);
            }
        }

        // Deduplicate adjacent identical tokens
        $dedup = [];
        foreach ($bnTokens as $t) {
            $t = trim($t);
            if (! $t) {
                continue;
            }
            if (empty($dedup) || end($dedup) !== $t) {
                $dedup[] = $t;
            }
        }

        // Deduplicate repeated halves
        $cnt = count($dedup);
        if ($cnt >= 4 && $cnt % 2 === 0) {
            $h = $cnt / 2;
            $f = array_slice($dedup, 0, $h);
            $s = array_slice($dedup, $h);
            if ($f === $s) {
                $dedup = $f;
            }
        }

        $finalName = $prefix.implode(' ', $dedup);
        // Clean redundant titles
        $finalName = preg_replace('/\bডাঃ\s+ডাঃ\b/u', 'ডাঃ', $finalName);
        $finalName = preg_replace('/\bঅধ্যাপক\s+ডাঃ\s+অধ্যাপক\s+ডাঃ\b/u', 'অধ্যাপক ডাঃ', $finalName);
        $finalName = preg_replace('/\s+/', ' ', trim($finalName));

        return $finalName;
    }

    /**
     * Phonetic transliterator with Bengali phonetics rules
     */
    public static function transliteratePhonetic(string $w): string
    {
        $map = [
            'uzzaman' => 'উজ্জামান', 'uddin' => 'উদ্দীন', 'ullah' => 'উল্লাহ',
            'quddus' => 'কুদ্দুস', 'kader' => 'কাদের', 'quader' => 'কাদের',
            'jannat' => 'জান্নাত', 'zannat' => 'জান্নাত', 'parveen' => 'পারভীন',
            'parvin' => 'পারভীন', 'tasnim' => 'তাসনিম', 'tanzila' => 'তানজিলা',
            'snigdha' => 'স্নিগ্ধা', 'dipti' => 'দীপ্তি', 'antara' => 'অন্তরা',
            'kundu' => 'কুন্ডু', 'chanda' => 'চন্দ', 'nandi' => 'নন্দী',
            'shanto' => 'শান্ত', 'shanta' => 'শান্তা', 'ananta' => 'অনন্ত',
            'dalia' => 'ডালিয়া', 'shuvra' => 'শুভ্রা', 'subhra' => 'শুভ্রা',
            'chowdhury' => 'চৌধুরী', 'khandaker' => 'খন্দকার', 'majumder' => 'মজুমদার',
            'sultana' => 'সুলতানা', 'farhana' => 'ফারহানা', 'hasan' => 'হাসান',
            'hossain' => 'হোসেন', 'rahman' => 'রহমান', 'shirazi' => 'শিরাজী',
        ];
        if (isset($map[$w])) {
            return $map[$w];
        }

        // Handle common English suffix patterns in Bengali names
        if (str_ends_with($w, 'que') && strlen($w) > 3) {
            $base = substr($w, 0, -3);

            return self::transliteratePhonetic($base).'ক';
        }
        if (str_ends_with($w, 'iah') && strlen($w) > 3) {
            $base = substr($w, 0, -3);

            return self::transliteratePhonetic($base).'িয়া';
        }
        if (str_ends_with($w, 'ia') && strlen($w) > 2) {
            $base = substr($w, 0, -2);

            return self::transliteratePhonetic($base).'িয়া';
        }
        if (str_ends_with($w, 'iur') && strlen($w) > 3) {
            $base = substr($w, 0, -3);

            return self::transliteratePhonetic($base).'িউর';
        }
        if (str_ends_with($w, 'iul') && strlen($w) > 3) {
            $base = substr($w, 0, -3);

            return self::transliteratePhonetic($base).'িউল';
        }
        if (str_starts_with($w, 'qu') && strlen($w) > 2) {
            return self::transliteratePhonetic('k'.substr($w, 2));
        }
        if (str_ends_with($w, 'uddin') && strlen($w) > 5) {
            $base = substr($w, 0, -5);

            return self::transliteratePhonetic($base).'উদ্দীন';
        }
        if (str_ends_with($w, 'ullah') && strlen($w) > 5) {
            $base = substr($w, 0, -5);

            return self::transliteratePhonetic($base).'উল্লাহ';
        }
        if (str_ends_with($w, 'uzzaman') && strlen($w) > 7) {
            $base = substr($w, 0, -7);

            return self::transliteratePhonetic($base).'উজ্জামান';
        }

        $rules = [
            'snigdha' => 'স্নিগ্ধা', 'dipti' => 'দীপ্তি', 'antara' => 'অন্তরা',
            'kht' => 'খত', 'st' => 'স্ত', 'sn' => 'স্ন', 'gdh' => 'গ্ধ',
            'nt' => 'ন্ত', 'nd' => 'ন্দ', 'nj' => 'ঞ্জ', 'pt' => 'প্ত',
            'zz' => 'জ্জ', 'dd' => 'দ্দ', 'bb' => 'ব্ব', 'mm' => 'ম্ম', 'll' => 'ল্ল',
            'kh' => 'খ', 'gh' => 'ঘ', 'ch' => 'চ', 'chh' => 'ছ', 'jh' => 'ঝ',
            'th' => 'থ', 'dh' => 'ধ', 'ph' => 'ফ', 'bh' => 'ভ', 'sh' => 'শ',
            'k' => 'ক', 'g' => 'গ', 'j' => 'জ', 't' => 'ত', 'd' => 'দ', 'n' => 'ন',
            'p' => 'প', 'f' => 'ফ', 'b' => 'ব', 'v' => 'ভ', 'm' => 'ম', 'r' => 'র',
            'l' => 'ল', 's' => 'স', 'h' => 'হ', 'z' => 'জ', 'w' => 'ও', 'y' => 'য়',
            'qui' => 'কি', 'qua' => 'কা', 'que' => 'কে', 'quo' => 'কো', 'qu' => 'ক',
            'ia' => 'িয়া', 'ea' => 'েয়া',
            'q' => 'ক', 'c' => 'ক', 'x' => 'ক্স',
            'ee' => 'ী', 'oo' => 'ূ', 'aa' => 'া', 'ai' => 'াই', 'oi' => 'ই',
            'ou' => 'ৌ', 'au' => 'ৌ', 'a' => 'া', 'e' => 'ে', 'i' => 'ি', 'o' => 'ো', 'u' => 'ু',
        ];

        $firstVowels = [
            'a' => 'আ', 'e' => 'এ', 'i' => 'ই', 'o' => 'ও', 'u' => 'উ',
        ];

        $output = '';
        $len = strlen($w);
        $i = 0;

        while ($i < $len) {
            $sub4 = substr($w, $i, 4);
            if (isset($rules[$sub4])) {
                $output .= $rules[$sub4];
                $i += 4;

                continue;
            }

            $sub3 = substr($w, $i, 3);
            if (isset($rules[$sub3])) {
                $output .= $rules[$sub3];
                $i += 3;

                continue;
            }

            $sub2 = substr($w, $i, 2);
            if (isset($rules[$sub2])) {
                $output .= $rules[$sub2];
                $i += 2;

                continue;
            }

            $char = $w[$i];
            if ($i === 0 && isset($firstVowels[$char])) {
                $output .= $firstVowels[$char];
                $i++;

                continue;
            }

            $output .= $rules[$char] ?? $char;
            $i++;
        }

        return $output;
    }

    /**
     * Clean designation to standard Bangla
     */
    public static function toPureBanglaDesignation(?string $rawDesignation): string
    {
        if (! $rawDesignation) {
            return 'বিশেষজ্ঞ চিকিৎসক';
        }

        $des = trim($rawDesignation);

        if (stripos($des, 'Professor & Head') !== false || stripos($des, 'Head of Department') !== false) {
            return 'অধ্যাপক ও বিভাগীয় প্রধান';
        }
        if (stripos($des, 'Associate Professor') !== false || stripos($des, 'সহযোগী অধ্যাপক') !== false) {
            return 'সহযোগী অধ্যাপক';
        }
        if (stripos($des, 'Assistant Professor') !== false || stripos($des, 'সহকারী অধ্যাপক') !== false) {
            return 'সহকারী অধ্যাপক';
        }
        if (stripos($des, 'Professor') !== false || stripos($des, 'অধ্যাপক') !== false) {
            return 'অধ্যাপক';
        }
        if (stripos($des, 'Senior Consultant') !== false || stripos($des, 'সিনিয়র কনসালট্যান্ট') !== false) {
            return 'সিনিয়র কনসালট্যান্ট';
        }
        if (stripos($des, 'Consultant') !== false || stripos($des, 'কনসালট্যান্ট') !== false) {
            return 'কনসালট্যান্ট';
        }
        if (stripos($des, 'Registrar') !== false || stripos($des, 'রেজিস্ট্রার') !== false) {
            return 'রেজিস্ট্রার';
        }

        return 'বিশেষজ্ঞ চিকিৎসক';
    }
}
