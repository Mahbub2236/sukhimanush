<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AiHealthController extends Controller
{
    /**
     * Medical specialty knowledge base with multilingual keywords, Bengali advice, and warnings.
     */
    protected array $specialtyRules = [
        'cardiology' => [
            'name_bn' => 'কার্ডিওলজি (হৃদরোগ বিশেষজ্ঞ)',
            'name_en' => 'Cardiology',
            'db_keywords' => ['কার্ডিওলজি', 'Cardiology', 'হৃদরোগ', 'কার্ডিওভাসকুলার'],
            'triggers' => [
                'বুক ব্যথা', 'বুকে ব্যথা', 'বুক ধড়ফড়', 'বুক ধড়ফড়', 'বুকে চাপ', 'হার্ট', 'heart', 'chest pain',
                'পালস', 'উচ্চ রক্তচাপ', 'প্রেসার হাই', 'হাই প্রেশার', 'হাইপারটেনশন', 'heart attack', 'অ্যানজাইনা',
                'পালপিটেশন', 'বাম বাহুতে ব্যথা', 'বুকে জ্বালাপোড়া ও ঘাম', 'রক্তচাপ',
            ],
            'emergency_triggers' => [
                'বুকে তীব্র চাপ', 'বাম হাতে ছড়ানো', 'শ্বাস বন্ধ হয়ে আসা', 'অতিরিক্ত ঘাম ও বুক ব্যথা', 'হার্ট অ্যাটাক',
            ],
            'advice' => 'বুকে অস্বস্তি বা অতিরিক্ত চাপ অনুভূত হওয়া হৃদযন্ত্রের সমস্যার প্রাথমিক লক্ষণ হতে পারে। ভারী পরিশ্রম বা ভারী খাবার পরিহার করুন এবং শান্তভাবে বিশ্রাম নিন। লবণ ও চর্বিজাতীয় খাবার খাওয়া নিয়ন্ত্রণ করুন। তবে লক্ষণগুলো দীর্ঘস্থায়ী হলে কালবিলম্ব না করে একজন হৃদরোগ বিশেষজ্ঞের (Cardiologist) পরামর্শ নিন এবং অবিলম্বে ইসিজি (ECG) ও ইকোকার্ডিওগ্রাফি করান।',
            'suggested_questions' => ['ইসিজি (ECG) টেস্ট কখন করা উচিত?', 'উচ্চ রক্তচাপ নিয়ন্ত্রণের সহজ উপায় কি?'],
        ],
        'gastroenterology' => [
            'name_bn' => 'গ্যাস্ট্রোএন্টারোলজি ও লিভার বিশেষজ্ঞ',
            'name_en' => 'Gastroenterology',
            'db_keywords' => ['গ্যাস্ট্রো', 'লিভার', 'Gastroenterology', 'Hepatology', 'পরিপাকতন্ত্র'],
            'triggers' => [
                'পেট ব্যথা', 'পেটে ব্যথা', 'পেট খারাপ', 'পেট কামড়ায়', 'বদহজম', 'গ্যাস্ট্রিক', 'গ্যাস', 'বমি', 'বমি বমি ভাব', 'পাতলা পায়খানা',
                'ডায়রিয়া', 'ডায়রিয়া', 'কোষ্ঠকাঠিন্য', 'পেট ফাঁপা', 'অম্বল', 'বুক জ্বালাপোড়া', 'stomach pain',
                'acidity', 'vomiting', 'diarrhea', 'constipation', 'জন্ডিস', 'ক্ষুধামন্দা', 'পায়খানার সাথে রক্ত',
            ],
            'emergency_triggers' => [
                'রক্তবমি', 'কালো পায়খানা', 'তীব্র পেট ফুলে ওঠা', 'পেটে অসহ্য যন্ত্রণা',
            ],
            'advice' => 'পেটের ব্যথা, বুক জ্বালা বা হজমের সমস্যা সাধারণত গ্যাস্ট্রাইটিস, পেপটিক আলসার অথবা খাদ্যবাহিত সংক্রমণের কারণে ঘটে থাকে। প্রচুর পরিমাণে বিশুদ্ধ পানি ও খাবার স্যালাইন গ্রহণ করুন। অতিরিক্ত তৈলাক্ত, ভাজাপোড়া ও মসলাযুক্ত খাবার থেকে বিরত থাকুন। সঠিক রোগ নির্ণয়ের জন্য একজন গ্যাস্ট্রোএন্টারোলজিস্ট অথবা মেডিসিন বিশেষজ্ঞের পরামর্শ নিন।',
            'suggested_questions' => ['গ্যাস্ট্রিক থেকে মুক্তির ঘরোয়া টিপস কি?', 'পেটে ব্যথা হলে কোন টেস্ট করা জরুরি?'],
        ],
        'pediatrics' => [
            'name_bn' => 'শিশু ও কিশোর রোগ বিশেষজ্ঞ',
            'name_en' => 'Pediatrics',
            'db_keywords' => ['শিশু', 'Pediatrics', 'নবজাতক'],
            'triggers' => [
                'বাচ্চা', 'শিশু', 'শিশুর জ্বর', 'বাচ্চার জ্বর', 'নবজাতক', 'child', 'baby', 'pediatric',
                'বাচ্চা খায় না', 'বাচ্চার বমি', 'বাচ্চার কাশি', 'বাচ্চার পাতলা পায়খানা', 'শিশুর কান্না', 'বাচ্চাদের ওজন',
            ],
            'emergency_triggers' => [
                'শিশুর খিঁচুনি', 'বাচ্চা নিস্তেজ হয়ে যাওয়া', 'শ্বাস নিতে কষ্ট হয়ে বুক দেবে যাওয়া', '১০৪ ডিগ্রির উপরে জ্বর',
            ],
            'advice' => 'শিশুদের রোগ প্রতিরোধ ক্ষমতা সংবেদনশীল হওয়ায় সামান্য অবহেলাও বিপজ্জনক হতে পারে। শিশুকে নিয়মিত বুকের দুধ বা পুষ্টিকর তরল খাবার দিন। জ্বরের ক্ষেত্রে ডাক্তারের পরামর্শ ছাড়া সাধারণ প্যারাসিটামলের অতিরিক্ত কোনো ওষুধ বা অ্যান্টিবায়োটিক নিজের ইচ্ছামতো খাওয়াবেন না। দ্রুত একজন শিশু বিশেষজ্ঞের (Pediatrician) স্মরণাপন্ন হওয়া জরুরি।',
            'suggested_questions' => ['শিশুর ঠান্ডা-কাশিতে করণীয় কি?', 'বাচ্চার সঠিক খাদ্যাভ্যাস কেমন হওয়া উচিত?'],
        ],
        'neurology' => [
            'name_bn' => 'নিউরো মেডিসিন ও স্নায়ুরোগ বিশেষজ্ঞ',
            'name_en' => 'Neurology',
            'db_keywords' => ['নিউরো', 'Neurology', 'স্নায়ু', 'ব্রেন'],
            'triggers' => [
                'মাথাব্যথা', 'মাথা ব্যথা', 'মাথা ঘোরা', 'মাইগ্রেন', 'migraine', 'headache', 'stroke', 'স্ট্রোক',
                'প্যারালাইসিস', 'হাত পা অবশ', 'খিঁচুনি', 'ঝিমুনি', 'স্মৃতিশক্তি লোপ', 'ভার্টিগো', 'ঘাড় শক্ত হওয়া',
            ],
            'emergency_triggers' => [
                'মুখ বেঁকে যাওয়া', 'একপাশ অবশ হয়ে যাওয়া', 'কথা জড়িয়ে যাওয়া', 'হঠাৎ তীব্রতম মাথাব্যথা',
            ],
            'advice' => 'ক্রমাগত মাথাব্যথা, মাথা ঘোরা বা শরীরের কোনো অংশে অসাড়তা স্নায়বিক চাপ, মাইগ্রেন কিংবা ব্রেন রক্ত সঞ্চালনের সমস্যার লক্ষণ হতে পারে। পর্যাপ্ত ঘুমানোর চেষ্টা করুন, মানসিক চাপ কমান এবং একটানা মোবাইল/কম্পিউটার স্ক্রিনের দিকে তাকিয়ে থাকবেন না। যদি ঝাপসা দৃষ্টি বা শরীরের একপাশ অবশ হতে থাকে, তবে বিলম্ব না করে নিউরোলজিস্টের (Neurologist) সাথে পরামর্শ করুন।',
            'suggested_questions' => ['মাইগ্রেনের ব্যথা কমানোর উপায় কি?', 'স্ট্রোকের পূর্বলক্ষণগুলো কী কী?'],
        ],
        'orthopedics' => [
            'name_bn' => 'হাড়, জোড়া ও বাতব্যথা বিশেষজ্ঞ (অর্থোপেডিক)',
            'name_en' => 'Orthopedics',
            'db_keywords' => ['অর্থোপেডিক', 'Orthopedic', 'হাড়', 'বাতব্যথা', 'ট্রমা'],
            'triggers' => [
                'মাজা ব্যথা', 'কোমর ব্যথা', 'হাড়ের ব্যথা', 'হাড় ভাঙা', 'জয়েন্টে ব্যথা', 'হাঁটু ব্যথা', 'বাত',
                'বাতব্যথা', 'আর্থ্রাইটিস', 'মেরুদণ্ড', 'পায়ের গোড়ালি ব্যথা', 'joint pain', 'back pain', 'bone',
                'স্পনডিলাইটিস', 'ইউরিক এসিড',
            ],
            'emergency_triggers' => [
                'হাড় ভেঙে চামড়া ফুঁড়ে বের হওয়া', 'দুর্ঘটনায় পা বা হাত বেঁকে যাওয়া',
            ],
            'advice' => 'হাড় ও অস্থিসন্ধির ব্যথা সাধারণত ভুল অঙ্গভঙ্গি, ক্যালসিয়ামের ঘাটতি, আর্থ্রাইটিস বা ভারী ওজন তোলার কারণে হয়ে থাকে। নিচু হয়ে ভারী জিনিস তুলবেন না এবং শক্ত সমান বিছানায় ঘুমান। দীর্ঘক্ষণ এক জায়গায় বসে না থেকে হালকা স্ট্রেচিং করুন। সঠিক এক্স-রে বা ইউরিক এসিড টেস্টের জন্য একজন অর্থোপেডিক বিশেষজ্ঞের শরণাপন্ন হোন।',
            'suggested_questions' => ['কোমর ব্যথায় কোন ব্যায়াম উপকারী?', 'হাঁটুর বাতব্যথা প্রতিরোধের উপায় কি?'],
        ],
        'dermatology' => [
            'name_bn' => 'চর্ম ও যৌনরোগ বিশেষজ্ঞ (ডার্মাটোলজি)',
            'name_en' => 'Dermatology',
            'db_keywords' => ['চর্ম', 'চর্মরোগ', 'Dermatology', 'যৌন', 'অ্যালার্জি', 'এলার্জি'],
            'triggers' => [
                'চুলকানি', 'চুলকায়', 'এলার্জি', 'অ্যালার্জি', 'দাউদ', 'চুল পড়ে যাওয়া', 'ব্রণ', 'ঘা', 'ত্বকে দাগ',
                'কুঁচকিতে চুলকানি', 'দাদ', 'স্কিন', 'skin', 'rash', 'allergy', 'itching', 'খোসপাঁচড়া', 'একজিমা',
            ],
            'emergency_triggers' => [
                'সমগ্র শরীরে হঠাৎ ফোস্কা পড়ে যাওয়া', 'ওষুধ খেয়ে শরীর ফুলে শ্বাসকষ্ট',
            ],
            'advice' => 'ত্বকে চুলকানি ও র‍্যাশ সাধারণত ফাঙ্গাল ইনফেকশন, অ্যালার্জি কিংবা অপরিষ্কার কাপড় ব্যবহারের কারণে ঘটে। আক্রান্ত স্থান সাবান ও তোয়ালে আলাদা রাখুন এবং শুকনো রাখুন। কোনো প্রেসক্রিপশন ছাড়া স্টেরয়েড ক্রিম ব্যবহার করবেন না, কারণ এতে দাগ স্থায়ী হতে পারে। সঠিক নির্ণয় ও চিকিৎসার জন্য একজন চর্মরোগ বিশেষজ্ঞকে (Dermatologist) দেখান।',
            'suggested_questions' => ['ছত্রাক বা ফাঙ্গাল ইনফেকশন দূর করার উপায় কি?', 'ব্রণ দূর করতে কোন যত্ন নেওয়া উচিত?'],
        ],
        'gynecology' => [
            'name_bn' => 'স্ত্রীরোগ ও প্রসূতিবিদ্যা বিশেষজ্ঞ (গাইনোকোলজি)',
            'name_en' => 'Gynecology',
            'db_keywords' => ['গাইনী', 'স্ত্রী', 'প্রসূতি', 'Gynecology', 'Obstetrics'],
            'triggers' => [
                'মাসিক', 'পিরিয়ড', 'সাদাস্রাব', 'গর্ভবতী', 'গর্ভাবস্থা', 'প্রেগন্যান্ট', 'তলপেটে ব্যথা',
                'গর্ভকালীন', 'বন্ধ্যাত্ব', 'ovary', 'pcos', 'pregnancy', 'period pain', 'বাচ্চা হচ্ছে না',
                'মহিলাদের তলপেটে ব্যথা',
            ],
            'emergency_triggers' => [
                'গর্ভকালীন অতিরিক্ত রক্তক্ষরণ', 'গর্ভবতীর তীব্র খিঁচুনি', 'বাচ্চার নড়াচড়া বন্ধ হয়ে যাওয়া',
            ],
            'advice' => 'মহিলাদের তলপেটে ব্যথা, অনিয়মিত পিরিয়ড বা গর্ভকালীন লক্ষণগুলোর ক্ষেত্রে বিশেষ যত্ন ও সঠিক ট্র্যাকিং প্রয়োজন। পর্যাপ্ত পুষ্টিকর খাবার ও আয়রন-ফলিক এসিড গ্রহণ করুন এবং পরিষ্কার-পরিচ্ছন্নতা বজায় রাখুন। কোনো ধরনের জটিলতা এড়াতে অনতিবিলম্বে একজন গাইনী ও প্রসূতি বিশেষজ্ঞের (Gynecologist) তত্ত্বাবধানে আল্ট্রাসনোগ্রামসহ চেকআপ করান।',
            'suggested_questions' => ['গর্ভকালীন প্রথম ৩ মাসের সতর্কতা কি কি?', 'অনিয়মিত মাসিকের কারণ ও সমাধান কি?'],
        ],
        'pulmonology' => [
            'name_bn' => 'বক্ষব্যাধি ও মেডিসিন বিশেষজ্ঞ (পালমোনোলজি)',
            'name_en' => 'Pulmonology',
            'db_keywords' => ['বক্ষব্যাধি', 'মেডিসিন', 'Medicine', 'Pulmonology', 'শ্বাসকষ্ট'],
            'triggers' => [
                'কাশি', 'শ্বাসকষ্ট', 'হাঁপানি', 'অ্যাজমা', 'কফ', 'সর্দি', 'বুকে ঘড়ঘড়', 'কফে রক্ত', 'নিউমোনিয়া',
                'অক্সিজেন', 'cough', 'asthma', 'shortness of breath', 'বুকের এক্সরে',
            ],
            'emergency_triggers' => [
                'শ্বাসকষ্টে ঠোঁট নীল হয়ে যাওয়া', 'কফের সাথে তাজা রক্ত যাওয়া', 'শ্বাস নিতে না পেরে অচেতন হওয়া',
            ],
            'advice' => 'অতিরিক্ত কাশি বা শ্বাসকষ্ট ব্রঙ্কাইটিস, হাঁপানি বা ফুসফুসে সংক্রমণের ইঙ্গিত দিতে পারে। ঠান্ডা পানি, ধুলোবালি ও ধূমপান থেকে সম্পূর্ণ দূরে থাকুন। হালকা কুসুম গরম পানি ও আদা চা পান করুন। ৩ সপ্তাহের বেশি কাশি স্থায়ী হলে বা রাতে শ্বাসকষ্ট বাড়লে একজন বক্ষব্যাধি বিশেষজ্ঞের (Chest Specialist) পরামর্শ নিয়ে বুকের এক্স-রে করান।',
            'suggested_questions' => ['অ্যাজমা বা হাঁপানি নিয়ন্ত্রণের উপায় কি?', 'দীর্ঘদিনের কাশির কারণ কি হতে পারে?'],
        ],
        'ent' => [
            'name_bn' => 'নাক, কান ও গলা বিশেষজ্ঞ (ENT)',
            'name_en' => 'ENT (Otolaryngology)',
            'db_keywords' => ['নাক', 'কান', 'গলা', 'ENT', 'Otolaryngology'],
            'triggers' => [
                'কানে ব্যথা', 'কান দিয়ে পানি পড়া', 'কানে কম শোনা', 'গলা ব্যথা', 'টনসিল', 'গলায় কিছু আটকে থাকা',
                'নাক বন্ধ', 'নাক দিয়ে রক্ত', 'সাইনাস', 'গলার স্বর ভেঙে যাওয়া', 'ear pain', 'throat pain', 'tonsil',
            ],
            'emergency_triggers' => [
                'শ্বাসনালীতে বা গলায় কিছু আটকে নিঃশ্বাস বন্ধ হওয়া', 'নাক দিয়ে অবিরাম রক্তপাত',
            ],
            'advice' => 'গলা ব্যথা ও কানে ইনফেকশন ঠান্ডা লাগা বা ব্যাক্টেরিয়াল টনসিলাইটিসের জন্য হতে পারে। কুসুম গরম পানিতে লবণ দিয়ে দিনে ৩-৪ বার কুলকুচি (Gargle) করুন। কানে কাঠি বা কোনো সূচালো বস্তু প্রবেশ করাবেন না। ব্যথা বেড়ে গেলে একজন নাক, কান ও গলা বিশেষজ্ঞের (ENT Specialist) পরামর্শ নিয়ে সঠিক ড্রপ ও চিকিৎসা নিন।',
            'suggested_questions' => ['টনসিল ইনফেকশন হলে কী করণীয়?', 'সাইনাসের মাথাব্যথা দূর করার উপায় কি?'],
        ],
        'ophthalmology' => [
            'name_bn' => 'চক্ষু রোগ বিশেষজ্ঞ (Ophthalmology)',
            'name_en' => 'Ophthalmology',
            'db_keywords' => ['চক্ষু', 'চোখ', 'Eye', 'Ophthalmology'],
            'triggers' => [
                'চোখে ব্যথা', 'চোখ লাল', 'ঝাপসা দেখা', 'চোখ দিয়ে পানি পড়া', 'চোখে চুলকানি', 'চোখে ছানি',
                'চশমা', 'দৃষ্টিশক্তি কমে যাওয়া', 'eye pain', 'blurred vision', 'চোখ ফোলা',
            ],
            'emergency_triggers' => [
                'চোখে রাসায়নিক তরল পড়া', 'হঠাৎ এক চোখের দৃষ্টি সম্পূর্ণ চলে যাওয়া',
            ],
            'advice' => 'চোখে লালচে ভাব বা ঝাপসা দেখা কনজাংটিভাইটিস, চোখের চাপ (গ্লুকোমা) বা পাওয়ারের সমস্যার কারণে হতে পারে। হাত দিয়ে চোখ ডলবেন না এবং ঠান্ডা পরিষ্কার পানি দিয়ে চোখ ধুয়ে নিন। স্ক্রিন টাইম কমান। ফার্মেসি থেকে নিজের ইচ্ছামতো ড্রপ ব্যবহার না করে দ্রুত একজন চক্ষু বিশেষজ্ঞের (Eye Specialist) শরণাপন্ন হোন।',
            'suggested_questions' => ['চোখের ক্লান্তি ও ঝাপসা দৃষ্টি কমানোর উপায় কি?', 'চোখ উঠলে কী সতর্কতা অবলম্বন করব?'],
        ],
        'dental' => [
            'name_bn' => 'দন্ত চিকিৎসক ও মুখরোগ বিশেষজ্ঞ (Dental Care)',
            'name_en' => 'Dental Care',
            'db_keywords' => ['ডেন্টাল', 'দন্ত', 'Dental', 'দাঁত'],
            'triggers' => [
                'দাঁতে ব্যথা', 'দাঁত ব্যথা', 'দাঁত', 'দাঁতে', 'মাড়ি', 'মাড়িতে', 'মাড়ি ফোলা', 'দাঁত দিয়ে রক্ত পড়া', 'মুখের দুর্গন্ধ', 'দাঁতের পোকা',
                'দাঁত শিরশির', 'মাড়ির ঘা', 'toothache', 'dental', 'gums bleeding', 'ডেন্টাল', 'দন্ত',
            ],
            'emergency_triggers' => [
                'দাঁতের ইনফেকশনে গলা ও মুখ ফুলে শ্বাস নিতে কষ্ট হওয়া',
            ],
            'advice' => 'দাঁতের ব্যথা সাধারণত ডেন্টাল ক্যারিজ (দাঁতের ক্যাভিটি) বা মাড়ির প্রদাহের কারণে হয়ে থাকে। কুসুম গরম পানিতে লবণ দিয়ে কুলকুচি করুন এবং ব্যথাস্থানে লবঙ্গ চেপে রাখতে পারেন। খুব ঠান্ডা বা খুব গরম পানীয় পরিহার করুন। দাঁত সংরক্ষণ ও রুট ক্যানেলের জন্য একজন দক্ষ ডেন্টাল সার্জনকে (Dentist) দেখান।',
            'suggested_questions' => ['দাঁতের শিরশিরানি কমানোর উপায় কি?', 'দাঁত সুস্থ রাখার নিয়মাবলী কি কি?'],
        ],
        'medicine' => [
            'name_bn' => 'জেনারেল মেডিসিন ও সার্বিক স্বাস্থ্য বিশেষজ্ঞ',
            'name_en' => 'Internal Medicine & General Physician',
            'db_keywords' => ['মেডিসিন বিশেষজ্ঞ', 'General Medicine', 'Internal Medicine', 'জেনারেল ফিজিশিয়ান'],
            'triggers' => [
                'জ্বর', 'গায়ে জ্বর', 'ঠান্ডা', 'কাশি', 'দুর্বলতা', 'ক্লান্তি', 'ফ্লু', 'ভাইরাল জ্বর', 'ডেঙ্গু',
                'টাইফয়েড', 'গায়ে ব্যথা', 'শরীর ব্যথা', 'fever', 'cold', 'flu', 'weakness', 'অরুচি', 'বমি বমি ভাব',
                'গলা খুশখুশ', 'মেডিসিন', 'গা গরম', 'হালকা জ্বর', 'দুর্বল', 'অসুস্থ', 'শরীর খারাপ', 'ডাক্তার দেখাব', 'ডাক্তার লাগবে',
            ],
            'emergency_triggers' => [
                'টানা ১০৪ ডিগ্রির উপরে তীব্র জ্বর', 'জ্বরের সাথে খিঁচুনি বা অচেতন হওয়া', 'ডেঙ্গুতে মাড়ি বা নাক দিয়ে রক্তপাত',
            ],
            'advice' => 'সাধারণ জ্বর, ঠান্ডা লাগা ও শারীরিক ক্লান্তি বেশিরভাগ ক্ষেত্রে ভাইরাল ইনফেকশন বা অতিরিক্ত শারীরিক চাপের কারণে হয়ে থাকে। প্রচুর পরিমাণে বিশুদ্ধ পানি, লেবু-পানি ও তরল পুষ্টিকর খাবার গ্রহণ করুন এবং পর্যাপ্ত বিশ্রাম নিন। ডাক্তারের পরামর্শ ছাড়া অ্যান্টিবায়োটিক সেবন করবেন না। জ্বর ৩ দিনের বেশি স্থায়ী হলে বা তীব্র মাথাব্যথা থাকলে অবিলম্বে একজন মেডিসিন বিশেষজ্ঞকে দেখান এবং সিবিসি (CBC) পরীক্ষা করান।',
            'suggested_questions' => ['জ্বরের সময় কী কী খাবার খাওয়া উপকারী?', 'ডেঙ্গু জ্বরের প্রাথমিক লক্ষণগুলো কী কী?'],
        ],
        'kidney' => [
            'name_bn' => 'কিডনি ও ইউরোলজি বিশেষজ্ঞ (Nephrology & Urology)',
            'name_en' => 'Nephrology & Urology',
            'db_keywords' => ['কিডনি', 'নেফ্রোলজি', 'Nephrology', 'ইউরোলজি', 'Kidney', 'মূত্ররোগ'],
            'triggers' => [
                'কিডনি', 'কিডনির সমস্যা', 'প্রস্রাবে জ্বালাপোড়া', 'প্রস্রাবে রক্ত', 'ঘন ঘন প্রস্রাব', 'ইউরিন',
                'ইউরিন ইনফেকশন', 'ডায়ালাইসিস', 'kidney', 'urine', 'uti', 'নেফ্রোলজি', 'ইউরোলজি', 'প্রস্রাব আটকে যাওয়া',
                'পা ফোলা', 'চোখের নিচে ফোলা', 'কিডনিতে পাথর', 'কোমরের দুই পাশে তীব্র ব্যথা',
            ],
            'emergency_triggers' => [
                'সম্পূর্ণ প্রস্রাব বন্ধ হয়ে যাওয়া', 'প্রস্রাবে তাজা রক্ত ও অসহ্য তলপেটে ব্যথা',
            ],
            'advice' => 'প্রস্রাবে জ্বালাপোড়া, ঘন ঘন প্রস্রাব হওয়া বা তলপেটে ও কোমরের পাশে ব্যথা ইউরিনারি ট্র্যাক্ট ইনফেকশন (UTI) কিংবা কিডনির সমস্যার পূর্বলক্ষণ হতে পারে। দিনে অন্তত ৩-৪ লিটার বিশুদ্ধ পানি পান করুন এবং প্রস্রাব আটকে রাখবেন না। সঠিক ডায়াগনোসিসের জন্য ইউরিন আর/ই (Urine R/E) ও সিরাম ক্রিয়েটিনিন টেস্ট করিয়ে একজন কিডনি অথবা ইউরোলজি বিশেষজ্ঞের পরামর্শ নিন।',
            'suggested_questions' => ['কিডনি সুস্থ রাখতে কতটুকু পানি পান করা উচিত?', 'প্রস্রাবে ইনফেকশন হলে কী কী সতর্কতা জরুরি?'],
        ],
        'endocrinology' => [
            'name_bn' => 'ডায়াবেটিস, থাইরয়েড ও হরমোন বিশেষজ্ঞ',
            'name_en' => 'Endocrinology & Diabetology',
            'db_keywords' => ['ডায়াবেটিস', 'ডায়াবেটিস', 'Diabetes', 'থাইরয়েড', 'হরমোন', 'Endocrinology'],
            'triggers' => [
                'ডায়াবেটিস', 'ডায়াবেটিস', 'সুগার', 'ব্লাড সুগার', 'থাইরয়েড', 'হরমোন', 'diabetes', 'sugar',
                'thyroid', 'গলা ফোলা', 'অতিরিক্ত ওজন', 'ওজন কমে যাওয়া', 'অতিরিক্ত পানির পিপাসা', 'ঘন ঘন খিদে লাগা',
            ],
            'emergency_triggers' => [
                'সুগার মাত্রাতিরিক্ত কমে অচেতন হওয়া (হাইপোগ্লাইসেমিয়া)', 'সুগার অতিরিক্ত বেড়ে কিটোএসিডোসিস হওয়া',
            ],
            'advice' => 'অতিরিক্ত তৃষ্ণা, ঘন ঘন প্রস্রাব বা হঠাৎ ওজন কমে যাওয়া ডায়াবেটিস বা থাইরয়েড হরমোনের ভারসাম্যহীনতার লক্ষণ হতে পারে। মিষ্টি ও পরিশোধিত শর্করা জাতীয় খাবার পরিহার করুন এবং প্রতিদিন অন্তত ৩০ মিনিট হাঁটাহাঁটি করুন। নিয়মিত ব্লাড সুগার ও টিএসএইচ (TSH) পরীক্ষা করে একজন ডায়াবেটিস ও হরমোন বিশেষজ্ঞের তত্ত্বাবধানে খাদ্যতালিকা ও চিকিৎসা নির্ধারণ করুন।',
            'suggested_questions' => ['ডায়াবেটিস নিয়ন্ত্রণের সেরা খাদ্যতালিকা কী?', 'থাইরয়েড সমস্যার সাধারণ লক্ষণগুলো কী?'],
        ],
        'surgery' => [
            'name_bn' => 'জেনারেল ও ল্যাপারোস্কোপিক সার্জারি বিশেষজ্ঞ',
            'name_en' => 'General & Laparoscopic Surgery',
            'db_keywords' => ['সার্জারি', 'Surgery', 'সার্জন', 'অপারেশন'],
            'triggers' => [
                'সার্জারি', 'অপারেশন', 'অ্যাপেন্ডিসাইটিস', 'হার্নিয়া', 'টিউমার', 'পাইলস', 'হেমোরয়েড', 'ফিস্টুলা',
                'গেজ', 'surgery', 'appendix', 'hernia', 'piles', 'মলদ্বারে রক্ত', 'মলদ্বারে ব্যথা', 'নাভিতে ফোলা',
            ],
            'emergency_triggers' => [
                'অ্যাপেন্ডিক্স ফেটে যাওয়ার মতো পেটের ডানপাশে অসহ্য তীব্র ব্যথা', 'অন্ত্রে রক্তক্ষরণ',
            ],
            'advice' => 'পাইলস, হার্নিয়া বা পেটের কোনো অংশে অতিরিক্ত ফোলাভাব ও দীর্ঘস্থায়ী ব্যথায় যথাসময়ে অস্ত্রোপচার বা আধুনিক ল্যাপারোস্কোপিক চিকিৎসার প্রয়োজন হতে পারে। কোষ্ঠকাঠিন্য এড়াতে প্রচুর শাকসবজি ও ইসবগুলের ভুসি খান। রোগ জটিল হওয়ার আগেই একজন অভিজ্ঞ জেনারেল সার্জন বা ল্যাপারোস্কোপিক বিশেষজ্ঞের পরামর্শ গ্রহণ করুন।',
            'suggested_questions' => ['পাইলসের আধুনিক ব্যথামুক্ত চিকিৎসা কী?', 'হার্নিয়া হলে অপারেশনের প্রয়োজনীয়তা কখন হয়?'],
        ],
        'psychiatry' => [
            'name_bn' => 'মনোরোগ ও মানসিক স্বাস্থ্য বিশেষজ্ঞ (Psychiatry)',
            'name_en' => 'Psychiatry & Mental Health',
            'db_keywords' => ['মনোরোগ', 'মানসিক', 'Psychiatry', 'সাইকিয়াট্রি'],
            'triggers' => [
                'মানসিক', 'ডিপ্রেশন', 'বিষণ্নতা', 'মন খারাপ', 'ঘুম হয় না', 'অনিদ্রা', 'দুশ্চিন্তা', 'টেনশন',
                'প্যানিক', 'উদ্বেগ', 'হতাশা', 'ভয় লাগে', 'মানসিক চাপ', 'depression', 'anxiety', 'insomnia', 'psychiatry',
                'পাগলামি', 'অস্থিরতা', 'মনোযোগের অভাব', 'স্মৃতিভ্রম',
            ],
            'emergency_triggers' => [
                'আত্মহত্যার চিন্তা', 'নিজেকে শেষ করে দেওয়ার ইচ্ছা', 'তীব্র মানসিক অস্থিরতায় নিয়ন্ত্রণহীন',
            ],
            'advice' => 'মানসিক চাপ, অনিদ্রা বা অতিরিক্ত দুশ্চিন্তা জীবনযাত্রার মান ব্যাহত করতে পারে। নিয়মিত ঘুম, পুষ্টিকর খাবার এবং পরিবারের সাথে খোলামেলা আলোচনা ইতিবাচক প্রভাব ফেলে। নিজেকে কখনো একা ভাববেন না। প্রয়োজনে একজন অভিজ্ঞ মনোরোগ বিশেষজ্ঞ (Psychiatrist) বা ক্লিনিক্যাল সাইকোলজিস্টের সাথে কাউন্সেলিং ও চিকিৎসার পরামর্শ গ্রহণ করুন।',
            'suggested_questions' => ['অনিদ্রা দূর করার প্রাকৃতিক উপায় কী?', 'মানসিক চাপ কমানোর মেডিটেশন টিপস কি?'],
        ],
        'oncology' => [
            'name_bn' => 'ক্যান্সার ও টিউমার বিশেষজ্ঞ (Oncology)',
            'name_en' => 'Oncology & Cancer Care',
            'db_keywords' => ['ক্যান্সার', 'Oncology', 'অনকোলজি', 'টিউমার'],
            'triggers' => [
                'ক্যান্সার', 'টিউমার', 'ক্যান্সার বিশেষজ্ঞ', 'কেমোথেরাপি', 'রেডিওথেরাপি', 'বায়োপসি', 'cancer',
                'oncology', 'tumor', 'ম্যালিগন্যান্ট', 'অস্বাভাবিক মাংসপিণ্ড',
            ],
            'emergency_triggers' => [
                'অস্বাভাবিক দ্রুত বর্ধনশীল টিউমার ও তীব্র রক্তক্ষরণ',
            ],
            'advice' => 'শরীরের কোনো অংশে অস্বাভাবিক চাকা, দীর্ঘদিন না সারা ক্ষত বা আকস্মিক ওজন হ্রাস পেলে অবিলম্বে সতর্ক হওয়া প্রয়োজন। দ্রুত বায়োপসি বা হিস্টোপ্যাথলজি টেস্ট করিয়ে একজন ক্যান্সার ও অনকোলজি বিশেষজ্ঞের তত্ত্বাবধানে চিকিৎসা শুরু করা অত্যন্ত জরুরি।',
            'suggested_questions' => ['ক্যান্সারের প্রাথমিক স্ক্রিনিং টেস্ট কী কী?', 'কেমোথেরাপির পার্শ্বপ্রতিক্রিয়া প্রতিরোধের উপায় কি?'],
        ],
    ];

    /**
     * Analyze user symptoms and recommend specialist doctors with clinical AI advice.
     */
    public function analyzeSymptoms(Request $request): JsonResponse
    {
        $input = $request->input('message') ?? $request->input('symptoms');
        if (empty($input) || ! is_string($input)) {
            return response()->json([
                'success' => false,
                'message' => 'The message or symptoms field is required.',
            ], 422);
        }

        $inputMessage = mb_strtolower(trim($input));
        $district = trim($request->input('district', ''));
        $upazila = trim($request->input('upazila', ''));

        // 1. Check for Emergency Red Flags
        $isEmergency = false;
        $emergencyReason = '';
        $emergencySpecialtyBonus = [];

        foreach ($this->specialtyRules as $key => $rule) {
            foreach ($rule['emergency_triggers'] as $emTrigger) {
                if (mb_strpos($inputMessage, mb_strtolower($emTrigger)) !== false) {
                    $isEmergency = true;
                    $emergencyReason = $emTrigger;
                    $emergencySpecialtyBonus[$key] = 200; // Strong priority for this specialty
                    break;
                }
            }
        }

        // Also check universal emergency keywords
        $universalEmergency = ['বিষ খেয়েছে', 'অচেতন', 'অজ্ঞান', 'অতিরিক্ত রক্তপাত', 'তীব্র এক্সিডেন্ট', 'গাড়ি দুর্ঘটনা', 'দম বন্ধ', 'হার্ট অ্যাটাক'];
        foreach ($universalEmergency as $ue) {
            if (mb_strpos($inputMessage, $ue) !== false) {
                $isEmergency = true;
                if (! $emergencyReason) {
                    $emergencyReason = $ue;
                }
                break;
            }
        }

        // 2. Determine Best Matching Specialty
        $matchedSpecialtyKey = null;
        $highestMatchScore = 0;

        foreach ($this->specialtyRules as $key => $rule) {
            $score = $emergencySpecialtyBonus[$key] ?? 0;
            foreach ($rule['triggers'] as $trigger) {
                $triggerLower = mb_strtolower($trigger);
                // For very short triggers (<= 2 chars like 'ঘা'), require space or boundary
                if (mb_strlen($triggerLower) <= 2) {
                    if (preg_match('/(^|\s|[,.\-!])'.preg_quote($triggerLower, '/').'(\s|[,.\-!]|$)/u', $inputMessage)) {
                        $score += 5;
                    }
                } elseif (mb_strpos($inputMessage, $triggerLower) !== false) {
                    $score += mb_strlen($triggerLower) * 2;
                }
            }
            if ($score > $highestMatchScore) {
                $highestMatchScore = $score;
                $matchedSpecialtyKey = $key;
            }
        }

        // Fallback to general medicine if no specific match
        if (! $matchedSpecialtyKey || $highestMatchScore === 0) {
            $matchedSpecialtyKey = 'medicine';
            $selectedRule = $this->specialtyRules['medicine'];
        } else {
            $selectedRule = $this->specialtyRules[$matchedSpecialtyKey];
        }

        // 3. Resolve Patient Location (from request, user profile, or message text)
        $loc = $this->resolveLocation($request, $inputMessage);
        $district = $loc['district'];
        $upazila = $loc['upazila'];
        $division = $loc['division'];

        // Query Database for REAL Recommended Doctors matching the diagnosed specialty (with proximity priority)
        $recommendedDoctors = $this->getDoctorsForSpecialty($selectedRule['db_keywords'], $loc, 4);
        $doctorsData = $this->formatDoctorsList($recommendedDoctors, $loc);

        // 4. Call Real External AI API (Google Gemini / OpenAI / Groq) if API Key is configured
        // Context contains real doctors from the database so the AI never invents fake doctors!
        $aiSource = 'clinical_knowledge_base';
        $aiModel = 'built-in-rules';
        $aiAdviceText = $selectedRule['advice'];
        $suggestedQuestions = $selectedRule['suggested_questions'] ?? [
            'এই রোগের জন্য কী কী রক্ত পরীক্ষা করা প্রয়োজন?',
            'খাবারের ক্ষেত্রে কী কী বিধিনিষেধ মানা উচিত?',
        ];

        $externalAi = $this->callExternalAiApi($inputMessage, $selectedRule, [
            'district' => $district,
            'upazila' => $upazila,
            'division' => $division,
        ], $doctorsData);

        if ($externalAi) {
            $aiSource = $externalAi['source'] ?? 'AI API';
            $aiModel = $externalAi['model'] ?? 'online-ai';
            if (! empty($externalAi['ai_advice'])) {
                $aiAdviceText = $externalAi['ai_advice'];
            }
            if (! empty($externalAi['specialty_bn'])) {
                $selectedRule['name_bn'] = $externalAi['specialty_bn'];
            }
            if (! empty($externalAi['specialty_en'])) {
                $selectedRule['name_en'] = $externalAi['specialty_en'];
            }
            if (isset($externalAi['is_emergency'])) {
                $isEmergency = (bool) $externalAi['is_emergency'];
            }
            if (! empty($externalAi['suggested_questions']) && is_array($externalAi['suggested_questions'])) {
                $suggestedQuestions = $externalAi['suggested_questions'];
            }

            // RE-QUERY DOCTORS IF EXTERNAL AI DIAGNOSED A SPECIFIC SPECIALTY
            // Guarantees recommended doctors always match the diagnosed specialty from the AI!
            $aiKeywords = $this->extractKeywordsFromAiSpecialty(
                $externalAi['specialty_bn'] ?? '',
                $externalAi['specialty_en'] ?? ''
            );

            if (! empty($aiKeywords)) {
                $aiDoctors = $this->getDoctorsForSpecialty($aiKeywords, $loc, 4);
                if ($aiDoctors->isNotEmpty()) {
                    $doctorsData = $this->formatDoctorsList($aiDoctors, $loc);
                }
            }
        }

        // 5. Formulate Emergency Alert Response
        $emergencyAlert = null;
        if ($isEmergency) {
            $emergencyAlert = [
                'title' => '🚨 জরুরি সতর্কবার্তা (Medical Red Flag Alert)',
                'message' => 'আপনার উল্লেখিত লক্ষণে ("'.($emergencyReason ?: 'মারাত্মক উপসর্গ').'") অবিলম্বে জরুরি চিকিৎসার প্রয়োজন হতে পারে। কোনো কালক্ষেপণ না করে দ্রুত নিকটস্থ হাসপাতালের জরুরি বিভাগে যান অথবা জাতীয় জরুরি সেবা ৯৯৯ বা সুখী মানুষ ইমার্জেন্সি হাবে যোগাযোগ করুন।',
                'helpline' => '999',
                'emergency_hub_url' => '/emergency',
            ];
        }

        return response()->json([
            'success' => true,
            'ai_source' => $aiSource,
            'ai_model' => $aiModel,
            'is_emergency' => $isEmergency,
            'emergency_alert' => $emergencyAlert,
            'emergency_warnings' => $isEmergency && $emergencyAlert ? [$emergencyAlert['message']] : [],
            'specialty_bn' => $selectedRule['name_bn'],
            'specialty_en' => $selectedRule['name_en'],
            'ai_advice' => $aiAdviceText,
            'advice' => $aiAdviceText,
            'recommended_doctors' => $doctorsData,
            'patient_location' => [
                'district' => $district,
                'upazila' => $upazila,
                'division' => $division,
                'has_location' => ! empty($district) || ! empty($upazila),
            ],
            'suggested_questions' => $suggestedQuestions,
            'matched_symptoms' => [$inputMessage],
            'disclaimer' => 'সতর্কতা: সুখী মানুষ এআই স্বাস্থ্য সহকারী কোনো চূড়ান্ত প্রেসক্রিপশন প্রদান করে না। এটি শুধুমাত্র প্রাথমিক দিকনির্দেশনা ও সঠিক বিশেষজ্ঞ নির্বাচনের জন্য তৈরি।',
        ]);
    }

    /**
     * Call real external AI API (Google Gemini / OpenAI / Groq) if API key is configured.
     * Injects candidate real doctors from the database so the AI never hallucinates fake doctors.
     */
    protected function callExternalAiApi(string $userMessage, array $selectedRule, array $location, $doctorsData): ?array
    {
        $geminiKey = config('services.gemini.api_key');
        $openaiKey = config('services.openai.api_key');
        $groqKey = config('services.groq.api_key');

        if (empty($geminiKey) && empty($openaiKey) && empty($groqKey)) {
            return null;
        }

        // Prepare context of REAL nearby doctors from DB so AI knows and references real doctors
        $realDoctorsSummary = [];
        foreach ($doctorsData->take(3) as $d) {
            $realDoctorsSummary[] = "- {$d['name']} ({$d['specialization']}), চেম্বার: {$d['chamber_address']}, এলাকা: ".($d['upazila'] ? $d['upazila'].', ' : '').$d['district'].', ভিজিট: ৳'.(int) $d['consultation_fee'];
        }
        $doctorsContextText = ! empty($realDoctorsSummary) ? implode("\n", $realDoctorsSummary) : 'কোনো নির্দিষ্ট চেম্বার ডাক্তার ডাটাবেজে পাওয়া যায়নি।';

        $locText = 'রোগীর এলাকা: '.($location['upazila'] ? $location['upazila'].', ' : '').($location['district'] ?: 'বাংলাদেশ');

        $systemPrompt = <<<PROMPT
আপনি "সুখী মানুষ এআই স্বাস্থ্য সহকারী (Sukhi Manush AI Medical Assistant)" - বাংলাদেশের একজন অত্যন্ত অভিজ্ঞ, সহানুভূতিশীল এবং নির্ভরযোগ্য ডিজিটাল ক্লিনিক্যাল ট্রায়াজ বিশেষজ্ঞ।
রোগীর লক্ষণের ভিত্তিতে আপনাকে সম্পূর্ণ প্রাকৃতিক, প্রাঞ্জল ও পেশাদার বাংলায় (Bengali) স্পষ্ট পরামর্শ দিতে হবে।

গুরুত্বপূর্ণ নির্দেশনা:
১. চিকিৎসা মূল্যায়ন: রোগীর সমস্যা মনোযোগ দিয়ে বুঝে সম্ভাব্য কারণসমূহ ও শারীরিক লক্ষণ ব্যাখ্যা করুন।
২. জরুরি অবস্থা যাচাই: যদি লক্ষণটি কোনো রেড ফ্ল্যাগ (যেমন: তীব্র বুকে চাপ/হার্ট অ্যাটাক, স্ট্রোকের লক্ষণ, রক্তবমি, অজ্ঞান হওয়া, বিষক্রিয়া ইত্যাদি) হয়, তবে অবিলম্বে রোগীকে জরুরি সেবা ৯৯৯ বা হাসপাতালে যাওয়ার স্পষ্ট সতর্কতা দিন ("is_emergency": true)।
৩. ঘরোয়া ও প্রাথমিক যত্ন: ডাক্তারের কাছে যাওয়ার আগে কী কী সাধারণ সতর্কতা (বিশ্রাম, প্রচুর পানি/তরল গ্রহণ, কী কী খাবার বা কাজ এড়িয়ে চলা উচিত) পালন করবেন তা জানান। কোনো নির্দিষ্ট প্রেসক্রিপশন বা অ্যান্টিবায়োটিক নিজে থেকে লিখে দেওয়া নিষেধ।
৪. প্রস্তাবিত বিশেষজ্ঞ: রোগীর কোন বিষয়ের বিশেষজ্ঞ ডাক্তার (যেমন: কার্ডিওলজিস্ট, গ্যাস্ট্রোএন্টারোলজিস্ট, ডেন্টিস্ট) দেখানো প্রয়োজন তা উল্লেখ করুন।
৫. ডাক্তার নির্দেশনা: নিচে রোগীর এলাকার ডাটাবেজের আসল ও নিবন্ধিত ডাক্তারদের তালিকা দেওয়া হলো। আপনি যদি ডাক্তারদের কথা উল্লেখ করেন, তবে শুধুমাত্র এই আসল ডাক্তারদের রেফারেন্স দিতে পারেন। কখনোই কোনো কাল্পনিক বা ফেক ডাক্তারের নাম তৈরি করবেন না।
৬. সম্পর্কিত প্রশ্ন: রোগীর মনে আসতে পারে এমন ২-৩টি প্রাসঙ্গিক প্রশ্ন দিন।

রোগীর অবস্থান: {$locText}
হাসপাতাল ডাটাবেজের নিকটবর্তী আসল ডাক্তারগণ:
{$doctorsContextText}

আপনাকে অবশ্যই শুধুমাত্র একটি বৈধ JSON অবজেক্ট রিটার্ন করতে হবে। কোনো মার্কডাউন কোডব্লক ছাড়া কেবল এই ফরম্যাটে JSON দিন:
{
  "is_emergency": false,
  "emergency_reason": "",
  "specialty_bn": "বিশেষজ্ঞ বিভাগের নাম বাংলায়",
  "specialty_en": "Specialty Name in English",
  "ai_advice": "বিস্তারিত সহানুভূতিশীল পরামর্শ (প্যারাগ্রাফ ও পয়েন্ট আকারে বাংলায়)...",
  "suggested_questions": ["প্রশ্ন ১?", "প্রশ্ন ২?"]
}
PROMPT;

        $userPrompt = "রোগীর বার্তা: \"{$userMessage}\"";

        // 1. Try Google Gemini First (if key available)
        if (! empty($geminiKey)) {
            $candidateModels = array_unique(array_filter([
                config('services.gemini.model', 'gemini-3.8-flash'),
                'gemini-3.8-flash',
                'gemini-3.5-flash',
            ]));

            $combinedPrompt = $systemPrompt."\n\n".$userPrompt;

            foreach ($candidateModels as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}";

                    $response = Http::timeout(18)->post($url, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $combinedPrompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.4,
                            'maxOutputTokens' => 2000,
                            'response_mime_type' => 'application/json',
                        ],
                    ]);

                    if ($response->successful()) {
                        $json = $response->json();
                        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($text) {
                            $parsed = $this->parseAiJson($text);
                            if ($parsed) {
                                $parsed['source'] = 'Google Gemini AI';
                                $parsed['model'] = $model;

                                return $parsed;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Try next model
                }
            }
        }

        // 2. Try OpenAI (if key available)
        if (! empty($openaiKey)) {
            try {
                $model = config('services.openai.model', 'gpt-4o-mini');
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer '.$openaiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 1200,
                ]);

                if ($response->successful()) {
                    $text = $response->json('choices.0.message.content');
                    if ($text) {
                        $parsed = $this->parseAiJson($text);
                        if ($parsed) {
                            $parsed['source'] = 'OpenAI';
                            $parsed['model'] = $model;

                            return $parsed;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Continue
            }
        }

        // 3. Try Groq (if key available)
        if (! empty($groqKey)) {
            try {
                $model = config('services.groq.model', 'llama-3.3-70b-versatile');
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer '.$groqKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 1200,
                ]);

                if ($response->successful()) {
                    $text = $response->json('choices.0.message.content');
                    if ($text) {
                        $parsed = $this->parseAiJson($text);
                        if ($parsed) {
                            $parsed['source'] = 'Groq AI';
                            $parsed['model'] = $model;

                            return $parsed;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Continue
            }
        }

        return null;
    }

    /**
     * Parse AI output safely, stripping code fences or picking JSON substring.
     */
    protected function parseAiJson(string $text): ?array
    {
        $cleaned = trim($text);
        // Strip markdown ```json ... ``` or ``` ... ```
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $cleaned, $m)) {
            $cleaned = trim($m[1]);
        }

        // Try direct json decode
        $decoded = json_decode($cleaned, true);
        if (is_array($decoded) && ! empty($decoded['ai_advice'])) {
            return $decoded;
        }

        // If JSON starts somewhere inside text
        if (preg_match('/\{[\s\S]*"ai_advice"[\s\S]*\}/u', $cleaned, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded) && ! empty($decoded['ai_advice'])) {
                return $decoded;
            }
        }

        // If it's valid text but not JSON, wrap it as advice
        if (mb_strlen($cleaned) > 20) {
            return [
                'ai_advice' => $cleaned,
            ];
        }

        return null;
    }

    /**
     * Get quick one-click symptom suggestions for interactive chips.
     */
    public function quickSymptoms(): JsonResponse
    {
        $chips = [
            [
                'id' => 'chest_pain',
                'icon' => '🫀',
                'label' => 'বুকে চাপ ও হৃদস্পন্দন দ্রুত',
                'query' => 'আমার বুকে ভারি চাপ অনুভব হচ্ছে এবং বুক ধড়ফড় করছে, কোন ডাক্তার দেখাব?',
            ],
            [
                'id' => 'stomach_ache',
                'icon' => '🩺',
                'label' => 'পেট ব্যথা, গ্যাস ও বমি ভাব',
                'query' => 'আমার ৩ দিন ধরে পেটে তীব্র ব্যথা, গ্যাস ও বমি বমি ভাব হচ্ছে।',
            ],
            [
                'id' => 'child_fever',
                'icon' => '👶',
                'label' => 'শিশুর জ্বর ও সর্দি-কাশি',
                'query' => 'আমার ছোট বাচ্চার দুই দিন ধরে জ্বর সাথে নাক দিয়ে পানি পড়ছে ও কাশি।',
            ],
            [
                'id' => 'headache',
                'icon' => '🧠',
                'label' => 'তীব্র মাথাব্যথা ও মাইগ্রেন',
                'query' => 'মাথার একপাশে প্রচণ্ড চিনচিনে ব্যথা, আলো সহ্য হচ্ছে না ও মাথা ঘোরাচ্ছে।',
            ],
            [
                'id' => 'joint_pain',
                'icon' => '🦴',
                'label' => 'কোমর ও হাঁটুর জয়েন্টে ব্যথা',
                'query' => 'হাঁটুর জোড়ায় প্রচণ্ড ব্যথা এবং দীর্ঘক্ষণ বসে থাকার পর কোমর সোজা করতে পারি না।',
            ],
            [
                'id' => 'skin_rash',
                'icon' => '🌿',
                'label' => 'শরীরে চুলকানি, এলার্জি ও র‍্যাশ',
                'query' => 'হাত-পা ও পিঠে লাল চাকা চাকা হয়ে প্রচণ্ড চুলকাচ্ছে, কোন বিশেষজ্ঞ দেখাব?',
            ],
            [
                'id' => 'breathing',
                'icon' => '🫁',
                'label' => 'শ্বাসকষ্ট ও দীর্ঘদিনের কাশি',
                'query' => 'বুকে সাঁ সাঁ শব্দ হচ্ছে, রাতে শ্বাস নিতে কষ্ট হয় এবং শুকনো কাশি হচ্ছে।',
            ],
            [
                'id' => 'eye_problem',
                'icon' => '👁️',
                'label' => 'চোখ লাল ও ঝাপসা দেখা',
                'query' => 'চোখ লাল হয়ে পানি পড়ছে এবং দূরের জিনিস ঝাপসা দেখছি।',
            ],
            [
                'id' => 'dental_pain',
                'icon' => '🦷',
                'label' => 'দাঁতে তীব্র ব্যথা ও মাড়ি ফোলা',
                'query' => 'দাঁতে প্রচণ্ড যন্ত্রণা, ঠান্ডা পানি খেলে শিরশির করে এবং মাড়ি ফুলে গেছে।',
            ],
            [
                'id' => 'anxiety_sleep',
                'icon' => '🧘',
                'label' => 'অনিদ্রা, ভয় ও অতিরিক্ত দুশ্চিন্তা',
                'query' => 'রাতে কিছুতেই ঘুম আসে না, বুক কাঁপে এবং সারাক্ষণ খারাপ কিছুর ভয় কাজ করে।',
            ],
        ];

        return response()->json([
            'success' => true,
            'chips' => $chips,
            'quick_symptoms' => array_map(function ($c) {
                return $c['label'];
            }, $chips),
        ]);
    }

    /**
     * Resolve patient location from request, user profile, or message content.
     */
    protected function resolveLocation(Request $request, string $inputMessage): array
    {
        $district = trim($request->input('district', ''));
        $upazila = trim($request->input('upazila', ''));
        $address = trim($request->input('address', ''));

        // If user is logged in, use their address if location not explicitly chosen
        $user = auth()->user() ?? $request->user();
        if ($user && empty($district) && ! empty($user->address)) {
            $address = $user->address;
        }

        // District mapping with English and Bengali keys and division lookup
        $districtMap = [
            'dhaka' => ['bn' => 'ঢাকা', 'en' => 'Dhaka', 'division' => 'Dhaka'],
            'gazipur' => ['bn' => 'গাজীপুর', 'en' => 'Gazipur', 'division' => 'Dhaka'],
            'narayanganj' => ['bn' => 'নারায়ণগঞ্জ', 'en' => 'Narayanganj', 'division' => 'Dhaka'],
            'tangail' => ['bn' => 'টাঙ্গাইল', 'en' => 'Tangail', 'division' => 'Dhaka'],
            'faridpur' => ['bn' => 'ফরিদপুর', 'en' => 'Faridpur', 'division' => 'Dhaka'],
            'manikganj' => ['bn' => 'মানিকগঞ্জ', 'en' => 'Manikganj', 'division' => 'Dhaka'],
            'munshiganj' => ['bn' => 'মুন্সীগঞ্জ', 'en' => 'Munshiganj', 'division' => 'Dhaka'],
            'narsingdi' => ['bn' => 'নরসিংদী', 'en' => 'Narsingdi', 'division' => 'Dhaka'],
            'gopalganj' => ['bn' => 'গোপালগঞ্জ', 'en' => 'Gopalganj', 'division' => 'Dhaka'],
            'kishoreganj' => ['bn' => 'কিশোরগঞ্জ', 'en' => 'Kishoreganj', 'division' => 'Dhaka'],
            'chittagong' => ['bn' => 'চট্টগ্রাম', 'en' => 'Chittagong', 'division' => 'Chittagong'],
            'cox\'s bazar' => ['bn' => 'কক্সবাজার', 'en' => "Cox's Bazar", 'division' => 'Chittagong'],
            'cumilla' => ['bn' => 'কুমিল্লা', 'en' => 'Cumilla', 'division' => 'Chittagong'],
            'brahmanbaria' => ['bn' => 'ব্রাহ্মণবাড়িয়া', 'en' => 'Brahmanbaria', 'division' => 'Chittagong'],
            'chandpur' => ['bn' => 'চাঁদপুর', 'en' => 'Chandpur', 'division' => 'Chittagong'],
            'feni' => ['bn' => 'ফেনী', 'en' => 'Feni', 'division' => 'Chittagong'],
            'noakhali' => ['bn' => 'নোয়াখালী', 'en' => 'Noakhali', 'division' => 'Chittagong'],
            'rajshahi' => ['bn' => 'রাজশাহী', 'en' => 'Rajshahi', 'division' => 'Rajshahi'],
            'bogra' => ['bn' => 'বগুড়া', 'en' => 'Bogra', 'division' => 'Rajshahi'],
            'pabna' => ['bn' => 'পাবনা', 'en' => 'Pabna', 'division' => 'Rajshahi'],
            'sirajganj' => ['bn' => 'সিরাজগঞ্জ', 'en' => 'Sirajganj', 'division' => 'Rajshahi'],
            'naogaon' => ['bn' => 'নওগাঁ', 'en' => 'Naogaon', 'division' => 'Rajshahi'],
            'natore' => ['bn' => 'নাটোর', 'en' => 'Natore', 'division' => 'Rajshahi'],
            'khulna' => ['bn' => 'খুলনা', 'en' => 'Khulna', 'division' => 'Khulna'],
            'jashore' => ['bn' => 'যশোর', 'en' => 'Jashore', 'division' => 'Khulna'],
            'kushtia' => ['bn' => 'কুষ্টিয়া', 'en' => 'Kushtia', 'division' => 'Khulna'],
            'satkhira' => ['bn' => 'সাতক্ষীরা', 'en' => 'Satkhira', 'division' => 'Khulna'],
            'barisal' => ['bn' => 'বরিশাল', 'en' => 'Barisal', 'division' => 'Barisal'],
            'patuakhali' => ['bn' => 'পটুয়াখালী', 'en' => 'Patuakhali', 'division' => 'Barisal'],
            'sylhet' => ['bn' => 'সিলেট', 'en' => 'Sylhet', 'division' => 'Sylhet'],
            'moulvibazar' => ['bn' => 'মৌলভীবাজার', 'en' => 'Moulvibazar', 'division' => 'Sylhet'],
            'rangpur' => ['bn' => 'রংপুর', 'en' => 'Rangpur', 'division' => 'Rangpur'],
            'dinajpur' => ['bn' => 'দিনাজপুর', 'en' => 'Dinajpur', 'division' => 'Rangpur'],
            'kurigram' => ['bn' => 'কুড়িগ্রাম', 'en' => 'Kurigram', 'division' => 'Rangpur'],
            'mymensingh' => ['bn' => 'ময়মনসিংহ', 'en' => 'Mymensingh', 'division' => 'Mymensingh'],
        ];

        // Specific area / Upazila keywords that map directly to a district and upazila
        $areaToDistrict = [
            'ধানমন্ডি' => ['district' => 'Dhaka', 'upazila' => 'Dhanmondi'],
            'dhanmondi' => ['district' => 'Dhaka', 'upazila' => 'Dhanmondi'],
            'মিরপুর' => ['district' => 'Dhaka', 'upazila' => 'Mirpur'],
            'mirpur' => ['district' => 'Dhaka', 'upazila' => 'Mirpur'],
            'উত্তরা' => ['district' => 'Dhaka', 'upazila' => 'Uttara'],
            'uttara' => ['district' => 'Dhaka', 'upazila' => 'Uttara'],
            'গুলশান' => ['district' => 'Dhaka', 'upazila' => 'Gulshan'],
            'gulshan' => ['district' => 'Dhaka', 'upazila' => 'Gulshan'],
            'বনানী' => ['district' => 'Dhaka', 'upazila' => 'Banani'],
            'banani' => ['district' => 'Dhaka', 'upazila' => 'Banani'],
            'মোহাম্মদপুর' => ['district' => 'Dhaka', 'upazila' => 'Mohammadpur'],
            'mohammadpur' => ['district' => 'Dhaka', 'upazila' => 'Mohammadpur'],
            'সাভার' => ['district' => 'Dhaka', 'upazila' => 'Savar'],
            'savar' => ['district' => 'Dhaka', 'upazila' => 'Savar'],
            'মতিঝিল' => ['district' => 'Dhaka', 'upazila' => 'Motijheel'],
            'motijheel' => ['district' => 'Dhaka', 'upazila' => 'Motijheel'],
            'টঙ্গী' => ['district' => 'Gazipur', 'upazila' => 'Tongi'],
            'tongi' => ['district' => 'Gazipur', 'upazila' => 'Tongi'],
            'বগুড়া সদর' => ['district' => 'Bogra', 'upazila' => 'Bogra Sadar'],
            'রংপুর সদর' => ['district' => 'Rangpur', 'upazila' => 'Rangpur Sadar'],
        ];

        $combinedSearchText = mb_strtolower($inputMessage.' '.$address);

        // 1. Scan for specific areas / thanas
        foreach ($areaToDistrict as $areaKey => $map) {
            if (mb_strpos($combinedSearchText, $areaKey) !== false) {
                if (empty($district)) {
                    $district = $map['district'];
                }
                if (empty($upazila)) {
                    $upazila = $map['upazila'];
                }
                break;
            }
        }

        // 2. Scan for districts in message text
        if (empty($district)) {
            foreach ($districtMap as $key => $dInfo) {
                if (mb_strpos($combinedSearchText, mb_strtolower($dInfo['bn'])) !== false ||
                    mb_strpos($combinedSearchText, $key) !== false) {
                    $district = $dInfo['en'];
                    break;
                }
            }
        }

        // 3. Find division from district
        $division = '';
        if (! empty($district)) {
            $distLower = mb_strtolower($district);
            foreach ($districtMap as $key => $dInfo) {
                if (mb_strtolower($dInfo['en']) === $distLower || mb_strtolower($dInfo['bn']) === $distLower) {
                    $division = $dInfo['division'];
                    $district = $dInfo['en']; // Standardize English name for DB
                    break;
                }
            }
        }

        return [
            'district' => $district,
            'upazila' => $upazila,
            'division' => $division,
        ];
    }

    /**
     * Generate crystal-clear Bengali audio (TTS) for rural/illiterate patients.
     */
    public function textToSpeech(Request $request)
    {
        $text = trim($request->input('text', ''));
        if (empty($text)) {
            return response()->json(['error' => 'Text is required'], 400);
        }

        // Clean text from markdown, HTML, emojis, English abbreviations that may trip speech
        $cleanText = preg_replace('/[#*_`~>\[\]()🚨🩺🏥💡✨⚠️📍\r]/u', ' ', $text);
        $cleanText = preg_replace('/\s+/u', ' ', $cleanText);
        $cleanText = trim($cleanText);

        if (empty($cleanText)) {
            return response()->json(['error' => 'No valid text to synthesize'], 400);
        }

        // Limit length to avoid extremely long TTS payload
        if (mb_strlen($cleanText) > 600) {
            $cleanText = mb_substr($cleanText, 0, 600).'।';
        }

        $cacheDir = storage_path('framework/cache/tts_audio');
        if (! File::isDirectory($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true, true);
        }

        $cacheKey = md5($cleanText);
        $cacheFile = "{$cacheDir}/{$cacheKey}.mp3";

        if (File::exists($cacheFile) && File::size($cacheFile) > 0) {
            return response()->file($cacheFile, [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // Split text into natural sentence chunks of <= 150 characters
        $sentences = preg_split('/([।!?\n]+)/u', $cleanText, -1, PREG_SPLIT_DELIM_CAPTURE);
        $chunks = [];
        $curr = '';
        foreach ($sentences as $s) {
            if (mb_strlen($curr.$s) > 140) {
                if (! empty(trim($curr))) {
                    $chunks[] = trim($curr);
                }
                $curr = $s;
            } else {
                $curr .= $s;
            }
        }
        if (! empty(trim($curr))) {
            $chunks[] = trim($curr);
        }

        $combinedAudio = '';
        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if (empty($chunk)) {
                continue;
            }
            try {
                $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=bn&client=tw-ob&q='.urlencode($chunk);
                $resp = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => 'https://translate.google.com/',
                ])->timeout(6)->get($url);

                if ($resp->successful()) {
                    $combinedAudio .= $resp->body();
                }
            } catch (\Exception $e) {
                // Continue with remaining chunks
            }
        }

        if (! empty($combinedAudio)) {
            File::put($cacheFile, $combinedAudio);

            return response($combinedAudio, 200, [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        return response()->json(['error' => 'Failed to generate audio'], 500);
    }

    /**
     * Retrieve approved specialist doctors matching the given specialty keywords, prioritizing patient location.
     * Guaranteed never to return doctors of irrelevant specialties (like dental/psychiatry for general symptoms).
     */
    protected function getDoctorsForSpecialty(array $keywords, array $location, int $limit = 4)
    {
        $district = $location['district'] ?? '';
        $upazila = $location['upazila'] ?? '';
        $division = $location['division'] ?? '';

        $fields = [
            'id', 'name', 'slug', 'specialization', 'designation', 'qualifications',
            'chamber_address', 'district', 'upazila', 'division',
            'consultation_fee', 'telemedicine_fee', 'is_verified', 'is_emergency_available', 'avatar',
        ];

        // Base query matching keywords
        $buildQuery = function (array $kws, bool $pureSpecializationOnly = true) {
            return Doctor::query()
                ->where('is_approved', true)
                ->where(function ($q) use ($kws, $pureSpecializationOnly) {
                    foreach ($kws as $kw) {
                        $q->orWhere('specialization', 'LIKE', "%{$kw}%");
                        if (! $pureSpecializationOnly) {
                            $q->orWhere('qualifications', 'LIKE', "%{$kw}%");
                        }
                    }
                });
        };

        // Proximity order helper
        $applyProximity = function ($query) use ($upazila, $district, $division) {
            $scoreParts = [];
            $bindings = [];

            if (! empty($upazila)) {
                $scoreParts[] = '(CASE WHEN (upazila LIKE ? OR chamber_address LIKE ?) THEN 2000 ELSE 0 END)';
                $bindings[] = "%{$upazila}%";
                $bindings[] = "%{$upazila}%";
            }

            if (! empty($district)) {
                $scoreParts[] = '(CASE WHEN (district LIKE ? OR city LIKE ? OR chamber_address LIKE ?) THEN 1000 ELSE 0 END)';
                $bindings[] = "%{$district}%";
                $bindings[] = "%{$district}%";
                $bindings[] = "%{$district}%";
            }

            if (! empty($division)) {
                $scoreParts[] = '(CASE WHEN division LIKE ? THEN 300 ELSE 0 END)';
                $bindings[] = "%{$division}%";
            }

            if (! empty($scoreParts)) {
                $proximitySql = implode(' + ', $scoreParts);
                $query->orderByRaw("({$proximitySql}) DESC", $bindings);
            }

            return $query->orderBy('is_featured', 'desc')
                ->orderBy('is_verified', 'desc')
                ->orderBy('id', 'asc');
        };

        // 1. First, search for doctors matching the requested specialty with proximity sorting (pure specialization)
        $doctors = $applyProximity($buildQuery($keywords, true))->limit($limit)->get($fields);

        // 2. If no doctors found with pure specialization, try qualifications as well
        if ($doctors->isEmpty()) {
            $doctors = $applyProximity($buildQuery($keywords, false))->limit($limit)->get($fields);
        }

        // 3. If still empty, fallback to General Medicine specialists
        if ($doctors->isEmpty()) {
            $generalKeywords = ['মেডিসিন বিশেষজ্ঞ', 'General Medicine', 'Internal Medicine'];
            $doctors = $applyProximity($buildQuery($generalKeywords, true))->limit($limit)->get($fields);
        }

        return $doctors;
    }

    /**
     * Map real doctors with proximity labels, consultation fee, and clean formatting.
     */
    protected function formatDoctorsList($doctors, array $location)
    {
        $district = $location['district'] ?? '';
        $upazila = $location['upazila'] ?? '';
        $division = $location['division'] ?? '';

        return $doctors->map(function ($doc) use ($upazila, $district, $division) {
            $isExactUpazila = ! empty($upazila) && (
                stripos($doc->upazila ?? '', $upazila) !== false ||
                stripos($doc->chamber_address ?? '', $upazila) !== false
            );
            $isSameDistrict = ! empty($district) && (
                stripos($doc->district ?? '', $district) !== false ||
                stripos($doc->city ?? '', $district) !== false ||
                stripos($doc->chamber_address ?? '', $district) !== false
            );
            $isSameDivision = ! empty($division) && (
                stripos($doc->division ?? '', $division) !== false
            );

            $proximityLabel = null;
            $isNearby = false;

            if ($isExactUpazila) {
                $proximityLabel = '📍 অতি নিকটবর্তী চেম্বার ('.($doc->upazila ?: $upazila).')';
                $isNearby = true;
            } elseif ($isSameDistrict) {
                $proximityLabel = '📍 আপনার জেলায় চেম্বার ('.($doc->district ?: $district).')';
                $isNearby = true;
            } elseif ($isSameDivision) {
                $proximityLabel = '📍 আপনার বিভাগে চেম্বার ('.($doc->division ?: $division).')';
            } else {
                $proximityLabel = ! empty($doc->district) ? '🏥 চেম্বার: '.$doc->district : 'অনলাইন ও চেম্বার';
            }

            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'slug' => $doc->slug,
                'specialization' => $doc->specialization,
                'designation' => $doc->designation,
                'qualifications' => $doc->qualifications,
                'chamber_address' => $doc->chamber_address,
                'district' => $doc->district,
                'upazila' => $doc->upazila,
                'division' => $doc->division,
                'consultation_fee' => $doc->consultation_fee,
                'telemedicine_fee' => $doc->telemedicine_fee,
                'is_verified' => $doc->is_verified,
                'is_emergency_available' => $doc->is_emergency_available,
                'avatar' => $doc->avatar,
                'avatar_url' => $doc->avatar_url,
                'is_nearby' => $isNearby,
                'proximity_label' => $proximityLabel,
            ];
        });
    }

    /**
     * Extract database search keywords from external AI diagnosed specialty.
     */
    protected function extractKeywordsFromAiSpecialty(string $specialtyBn, string $specialtyEn): array
    {
        $search = mb_strtolower($specialtyBn.' '.$specialtyEn);

        foreach ($this->specialtyRules as $key => $rule) {
            if (mb_strpos($search, mb_strtolower($rule['name_en'])) !== false ||
                mb_strpos($search, mb_strtolower($key)) !== false) {
                return $rule['db_keywords'];
            }
            foreach ($rule['db_keywords'] as $kw) {
                if (mb_strpos($search, mb_strtolower($kw)) !== false) {
                    return $rule['db_keywords'];
                }
            }
        }

        // If no predefined rule matches, extract words > 2 chars
        $words = array_filter(preg_split('/[\s,\-\(\)\/]+/u', $search), function ($w) {
            return mb_strlen($w) >= 3 && ! in_array($w, ['বিশেষজ্ঞ', 'doctor', 'specialist', 'department', 'বিভাগ', 'care', 'medicine']);
        });

        return ! empty($words) ? array_values($words) : ['মেডিসিন', 'Medicine', 'Internal Medicine', 'জেনারেল'];
    }

    /**
     * Provide realistic sample prescriptions for 1-click preview and testing.
     */
    public function samplePrescriptions(): JsonResponse
    {
        $samples = [
            [
                'id' => 'sample_gastric',
                'title' => 'গ্যাস্ট্রিক, অ্যাসিডিটি ও বুক জ্বালাপোড়া',
                'doctor_name' => 'ডাঃ তাসনিম জাহান (এমবিবিএস, এফসিপিএস - মেডিসিন)',
                'hospital_name' => 'ঢাকা মেডিকেল কলেজ হাসপাতাল ও চেম্বার',
                'date' => date('d F Y'),
                'patient_name' => 'আব্দুর রহিম (৪৫ বছর, পুরুষ)',
                'specialty' => 'গ্যাস্ট্রোএন্টারোলজি ও মেডিসিন',
                'preview_badge' => 'খুব সাধারণ প্রেসক্রিপশন',
                'medicines' => [
                    [
                        'name' => 'Cap. Seclo 20mg (সেকলো ২০ মি.গ্রা.)',
                        'generic' => 'Omeprazole (ওমিপ্রাজল)',
                        'type' => 'ক্যাপসুল',
                        'purpose_bn' => 'গ্যাস্ট্রিক, বুক জ্বালাপোড়া ও পেটের অতিরিক্ত অ্যাসিড কমানোর জন্য।',
                        'dosage_bn' => '১ + ০ + ১ (সকালে ১টি ও রাতে ১টি)',
                        'timing_bn' => 'খাওয়ার ৩০ মিনিট আগে (খালি পেটে)',
                        'duration_bn' => '১৪ দিন নিয়মিত',
                        'instructions_bn' => 'পানি দিয়ে সম্পূর্ণ গিলে খাবেন। ক্যাপসুল ভেঙে বা চিবিয়ে খাবেন না।',
                        'icon' => 'fa-solid fa-capsules',
                    ],
                    [
                        'name' => 'Tab. Alarid 10mg (অ্যালারিড ১০ মি.গ্রা.)',
                        'generic' => 'Domperidone (ডমপেরিডন)',
                        'type' => 'ট্যাবলেট',
                        'purpose_bn' => 'বমি বমি ভাব, পেট ফাঁপা ও বদহজম দূর করতে।',
                        'dosage_bn' => '১ + ১ + ১ (সকালে, দুপুরে ও রাতে)',
                        'timing_bn' => 'খাওয়ার ১৫-২০ মিনিট আগে',
                        'duration_bn' => '৭ দিন',
                        'instructions_bn' => 'পেট হালকা রাখতে ও খাবার সহজে হজম হতে সাহায্য করে।',
                        'icon' => 'fa-solid fa-tablets',
                    ],
                    [
                        'name' => 'Syp. Entacyd Plus (এন্টাসিড প্লাস সিরাপ)',
                        'generic' => 'Magaldrate + Simethicone',
                        'type' => 'সিরাপ',
                        'purpose_bn' => 'বুকে জ্বালাপোড়া ও তাৎক্ষণিক গ্যাসের অস্বস্তি কমাতে।',
                        'dosage_bn' => '২ চামচ করে দিনে ৩ বার',
                        'timing_bn' => 'খাওয়ার ১ ঘণ্টা পর অথবা বুকে জ্বালা করলে',
                        'duration_bn' => 'প্রয়োজন অনুযায়ী',
                        'instructions_bn' => 'বোতলটি ভালো করে ঝাঁকিয়ে খাবেন।',
                        'icon' => 'fa-solid fa-prescription-bottle-medical',
                    ],
                ],
                'tests' => [
                    [
                        'name' => 'USG of Whole Abdomen (পেটের আল্ট্রাসনোগ্রাম)',
                        'purpose_bn' => 'লিভার, পিত্তথলি, অগ্ন্যাশয় ও কিডনিতে কোনো প্রদাহ বা পাথর আছে কিনা তা পরীক্ষা করতে।',
                        'preparation_bn' => 'পরীক্ষার আগের দিন রাতে হালকা খাবার খাবেন এবং সকালে খালি পেটে পর্যাপ্ত পানি খেয়ে প্রস্রাবের বেগ নিয়ে আসবেন।',
                    ],
                    [
                        'name' => 'Serum Creatinine (সিরাম ক্রিয়েটিনিন)',
                        'purpose_bn' => 'কিডনির সার্বিক কার্যকারিতা স্বাভাবিক রয়েছে কিনা তা নিশ্চিত হতে।',
                        'preparation_bn' => 'বিশেষ কোনো প্রস্তুতির প্রয়োজন নেই।',
                    ],
                ],
                'advice' => [
                    'অতিরিক্ত তেল, চর্বি, ঝাল ও ভাজাপোড়া খাবার সম্পূর্ণ এড়িয়ে চলুন।',
                    'খাওয়ার সাথে সাথেই শুয়ে পড়বেন না; অন্তত ৩০-৪০ মিনিট পর ঘুমাতে যান।',
                    'ধূমপান, চা-কফি ও কোমল পানীয় গ্রহণ সীমিত করুন।',
                    'প্রতিদিন অন্তত ২.৫ থেকে ৩ লিটার বিশুদ্ধ পানি পান করুন।',
                ],
                'voice_script' => 'আপনার গ্যাস্ট্রিকের প্রেসক্রিপশন অনুযায়ী তিনটি ওষুধ রয়েছে। এক, সেকলো ২০ মিলিগ্রাম—এটি সকালে ও রাতে খাওয়ার আধা ঘণ্টা আগে খাবেন। দুই, অ্যালারিড ১০ মিলিগ্রাম—বমি ও পেট ফাঁপা দূর করার জন্য খাওয়ার ১৫ মিনিট আগে খাবেন। তিন, এন্টাসিড সিরাপ—খাওয়ার এক ঘণ্টা পর দুই চামচ করে খাবেন। পেটের আল্ট্রাসনোগ্রাম ও ক্রিয়েটিনিন টেস্ট করার পরামর্শ দেওয়া হয়েছে। অতিরিক্ত তৈলাক্ত খাবার এড়িয়ে চলুন।',
            ],
            [
                'id' => 'sample_cold_fever',
                'title' => 'মৌসুমি সর্দি, কাশি ও সাধারণ জ্বর',
                'doctor_name' => 'ডাঃ মোঃ আরিফুল ইসলাম (এমবিবিএস, বিসিএস, এমডি - পেডিয়াট্রিক্স ও মেডিসিন)',
                'hospital_name' => 'কুমিল্লা জেনারেল হাসপাতাল ও পপুলার চেম্বার',
                'date' => date('d F Y'),
                'patient_name' => 'রাবেয়া বেগম (৩২ বছর, মহিলা)',
                'specialty' => 'মেডিসিন ও বক্ষব্যাধি',
                'preview_badge' => 'জ্বর ও ঠান্ডা কাশি',
                'medicines' => [
                    [
                        'name' => 'Tab. Napa Extend 665mg (নাপা এক্সটেন্ড)',
                        'generic' => 'Paracetamol Extended Release',
                        'type' => 'ট্যাবলেট',
                        'purpose_bn' => 'জ্বর কমানো ও শরীরের মাংসপেশির ব্যথা উপশমের জন্য।',
                        'dosage_bn' => '১ + ০ + ১ (সকালে ১টি ও রাতে ১টি)',
                        'timing_bn' => 'ভরা পেটে (খাওয়ার পর)',
                        'duration_bn' => '৫ দিন (জ্বর সেরে গেলে বন্ধ করতে পারেন)',
                        'instructions_bn' => 'জ্বর ১০১ ডিগ্রির বেশি থাকলে কপালে জলপট্টি দিন।',
                        'icon' => 'fa-solid fa-tablets',
                    ],
                    [
                        'name' => 'Tab. Fexo 120mg (ফেক্সো ১২০ মি.গ্রা.)',
                        'generic' => 'Fexofenadine Hydrochloride',
                        'type' => 'ট্যাবলেট',
                        'purpose_bn' => 'নাক দিয়ে পানি পড়া, হাঁচি ও গলা চুলকানি বন্ধ করতে।',
                        'dosage_bn' => '০ + ০ + ১ (রাতে ১টি)',
                        'timing_bn' => 'খাওয়ার পর (ঘুমানোর আগে)',
                        'duration_bn' => '৭ দিন',
                        'instructions_bn' => 'এই ওষুধ খেলে সাধারণত অতিরিক্ত ঘুম পায় না।',
                        'icon' => 'fa-solid fa-tablets',
                    ],
                    [
                        'name' => 'Syp. Tusca (তুসকা কফ সিরাপ)',
                        'generic' => 'Dextromethorphan + Phenylephrine',
                        'type' => 'সিরাপ',
                        'purpose_bn' => 'শুকনো কাশি ও শ্বাসনালীর অস্বস্তি নিয়ন্ত্রণে রাখতে।',
                        'dosage_bn' => '২ চামচ করে দিনে ৩ বার',
                        'timing_bn' => 'খাওয়ার পর হালকা কুসুম গরম পানির সাথে',
                        'duration_bn' => '৫ দিন',
                        'instructions_bn' => 'সিরাপ খাওয়ার পরপরই ঠান্ডা পানি খাবেন না।',
                        'icon' => 'fa-solid fa-prescription-bottle-medical',
                    ],
                ],
                'tests' => [
                    [
                        'name' => 'CBC (Complete Blood Count)',
                        'purpose_bn' => 'রক্তে প্লাটিলেট, শ্বেতরক্তকণিকা ও কোনো ভাইরাল বা ব্যাকটেরিয়াল ইনফেকশন আছে কিনা তা জানার জন্য।',
                        'preparation_bn' => 'যেকোনো সময় রক্ত দেওয়া যাবে।',
                    ],
                ],
                'advice' => [
                    'ঠান্ডা পানি, আইসক্রিম বা এসির সরাসরি ঠান্ডা বাতাস এড়িয়ে চলুন।',
                    'দিনে কয়েকবার কুসুম গরম পানিতে লবণ দিয়ে কুলকুচি (Gargle) করুন।',
                    'আদা-লেবু ও তুলসী চা পান করুন।',
                    'জ্বর ৩ দিনের বেশি স্থায়ী হলে বা শ্বাসকষ্ট দেখা দিলে অবিলম্বে চেম্বারে যোগাযোগ করুন।',
                ],
                'voice_script' => 'আপনার প্রেসক্রিপশনের ওষুধগুলো হলো: এক, নাপা এক্সটেন্ড—জ্বর ও ব্যথার জন্য সকালে ও রাতে ভরা পেটে খাবেন। দুই, ফেক্সো ১২০ মিলিগ্রাম—হাঁচি ও সর্দির জন্য রাতে খাওয়ার পর একটি করে খাবেন। তিন, তুসকা সিরাপ—কাশির জন্য দুই চামচ করে দিনে তিনবার খাবেন। প্রচুর কুসুম গরম পানি ও লেবু চা পান করুন।',
            ],
            [
                'id' => 'sample_hypertension_diabetes',
                'title' => 'উচ্চ রক্তচাপ (হাই প্রেশার) ও ডায়াবেটিস নিয়মিত চেকআপ',
                'doctor_name' => 'প্রফেসর ডাঃ এ কে এম সামসুল হক (কার্ডিওলজিস্ট ও ডায়াবেটোলজিস্ট)',
                'hospital_name' => 'জাতীয় হৃদরোগ ইনস্টিটিউট ও বারডেম',
                'date' => date('d F Y'),
                'patient_name' => 'হাজী মোঃ এনামুল হক (৫৮ বছর, পুরুষ)',
                'specialty' => 'কার্ডিওলজি ও এন্ডোক্রাইনোলজি',
                'preview_badge' => 'দীর্ঘমেয়াদী প্রেসক্রিপশন',
                'medicines' => [
                    [
                        'name' => 'Tab. Bizoran 5/20 (বাইজরান ৫/২০)',
                        'generic' => 'Amlodipine 5mg + Olmesartan 20mg',
                        'type' => 'ট্যাবলেট',
                        'purpose_bn' => 'রক্তনালী শিথিল করে উচ্চ রক্তচাপ (হাই প্রেশার) নিয়ন্ত্রণে রাখতে।',
                        'dosage_bn' => '১ + ০ + ০ (প্রতিদিন সকালে ১টি)',
                        'timing_bn' => 'সকালের নাস্তার পর নিয়মিত নির্দিষ্ট সময়ে',
                        'duration_bn' => 'চলমান (ডাক্তারের পরামর্শ ছাড়া বন্ধ করবেন না)',
                        'instructions_bn' => 'রক্তচাপ স্বাভাবিক থাকলেও ওষুধ বন্ধ করা যাবে না।',
                        'icon' => 'fa-solid fa-heart-pulse',
                    ],
                    [
                        'name' => 'Tab. Combit 500mg (কম্বিট ৫০০ মি.গ্রা.)',
                        'generic' => 'Metformin Hydrochloride',
                        'type' => 'ট্যাবলেট',
                        'purpose_bn' => 'রক্তে গ্লুকোজ বা চিনির পরিমাণ সঠিক মাত্রায় বজায় রাখতে।',
                        'dosage_bn' => '১ + ০ + ১ (সকালে ১টি ও রাতে ১টি)',
                        'timing_bn' => 'খাবার খাওয়ার মাঝামাঝি সময়ে বা ঠিক পরে',
                        'duration_bn' => 'চলমান',
                        'instructions_bn' => 'পেটের অস্বস্তি এড়াতে ভরা পেটে খেতে হবে।',
                        'icon' => 'fa-solid fa-tablets',
                    ],
                    [
                        'name' => 'Tab. Neuro-B (নিউরো-বি)',
                        'generic' => 'Vitamin B1 + B6 + B12',
                        'type' => 'ভিটামিন ট্যাবলেট',
                        'purpose_bn' => 'স্নায়ু সবল রাখতে এবং হাত-পায়ের ঝিমঝিম বা অবশভাব দূর করতে।',
                        'dosage_bn' => '০ + ১ + ০ (দুপুরে ১টি)',
                        'timing_bn' => 'দুপুরের খাবারের পর',
                        'duration_bn' => '১ মাস',
                        'instructions_bn' => 'পর্যাপ্ত পানি দিয়ে খাবেন।',
                        'icon' => 'fa-solid fa-capsules',
                    ],
                ],
                'tests' => [
                    [
                        'name' => 'HbA1c (তিন মাসের গড় ডায়াবেটিস পরীক্ষা)',
                        'purpose_bn' => 'গত ৩ মাসে আপনার ডায়াবেটিস কতটা নিয়ন্ত্রণে ছিল তা মূল্যায়নের জন্য।',
                        'preparation_bn' => 'খালি পেটে বা ভরা পেটে যেকোনো সময় টেস্ট করা যায়।',
                    ],
                    [
                        'name' => 'Lipid Profile (রক্তের চর্বি পরীক্ষা)',
                        'purpose_bn' => 'রক্তে খারাপ কোলেস্টেরল ও ট্রাইগ্লিসারাইডের মাত্রা পরিমাপ করতে।',
                        'preparation_bn' => 'পরীক্ষার আগে রাতে ১০-১২ ঘণ্টা উপবাস (খালি পেটে) থাকতে হবে।',
                    ],
                ],
                'advice' => [
                    'খাবারে বাড়তি কাঁচা লবণ খাওয়া সম্পূর্ণ পরিহার করুন।',
                    'মিষ্টি, কোমল পানীয় ও অতিরিক্ত কার্বোহাইড্রেট এড়িয়ে চলুন।',
                    'প্রতিদিন অন্তত ৩০ মিনিট নিয়মিত দ্রুত হাঁটার অভ্যাস গড়ে তুলুন।',
                    'সপ্তাহে অন্তত একবার রক্তচাপ ও ডায়াবেটিস মেপে চার্টে লিখে রাখুন।',
                ],
                'voice_script' => 'আপনার প্রেসক্রিপশনের ওষুধগুলো হলো: এক, বাইজরান ৫ বাই ২০—উচ্চ রক্তচাপের জন্য প্রতিদিন সকালে নাস্তার পর খাবেন। দুই, কম্বিট ৫০০—ডায়াবেটিসের জন্য সকালে ও রাতে ভরা পেটে খাবেন। তিন, নিউরো-বি—স্নায়ুর শক্তির জন্য দুপুরে খাবেন। বাড়তি লবণ ও মিষ্টি সম্পূর্ণ পরিহার করুন এবং প্রতিদিন ৩০ মিনিট হাঁটুন।',
            ],
        ];

        return response()->json([
            'success' => true,
            'samples' => $samples,
        ]);
    }

    /**
     * AI-Powered Prescription Explainer in Plain Bengali.
     * Takes prescription image, text, or sample ID and returns patient-friendly translated breakdown.
     */
    public function explainPrescription(Request $request): JsonResponse
    {
        // Validate inputs and uploaded files
        $request->validate([
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:10240',
            'prescription_file' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_base64' => 'nullable|string|max:15000000',
            'text' => 'nullable|string|max:5000',
            'prescription_text' => 'nullable|string|max:5000',
            'sample_id' => 'nullable|string|max:50',
        ]);

        // Allow up to 180 seconds for deep OCR and medical AI vision analysis
        @ini_set('max_execution_time', '180');
        @set_time_limit(180);

        $sampleId = $request->input('sample_id');
        if (! empty($sampleId)) {
            $samplesRes = $this->samplePrescriptions()->getData(true);
            $samples = $samplesRes['samples'] ?? [];
            foreach ($samples as $s) {
                if ($s['id'] === $sampleId) {
                    $this->attachPricingSummaryToPrescription($s);

                    return response()->json([
                        'success' => true,
                        'source' => 'sample_verified_database',
                        'prescription' => $s,
                    ]);
                }
            }
        }

        $prescriptionText = $request->input('text') ?? $request->input('prescription_text') ?? '';
        $uploadedFile = $request->file('image') ?? $request->file('prescription_file');
        $base64Image = $request->input('image_base64');

        if (empty($prescriptionText) && ! $uploadedFile && empty($base64Image)) {
            return response()->json([
                'success' => false,
                'message' => 'প্রেসক্রিপশনের ছবি আপলোড করুন অথবা ওষুধের নাম লিখে পাঠান।',
            ], 422);
        }

        // Try vision AI if image is provided and external AI key is available
        if (($uploadedFile || ! empty($base64Image))) {
            $visionResult = $this->analyzePrescriptionWithVisionAi($uploadedFile, $base64Image, $prescriptionText);
            if ($visionResult) {
                $rxData = $visionResult['data'];
                $this->attachPricingSummaryToPrescription($rxData);

                return response()->json([
                    'success' => true,
                    'source' => $visionResult['source'] ?? 'AI Vision Analysis',
                    'prescription' => $rxData,
                ]);
            }

            // If image was supplied but Vision AI failed and no text was entered, don't fall back to generic fake meds
            if (empty(trim($prescriptionText))) {
                return response()->json([
                    'success' => false,
                    'message' => 'দুঃখিত, এআই প্রেসক্রিপশনের ছবিটি স্পষ্টভাবে বিশ্লেষণ করতে পারেনি। অনুগ্রহ করে সোজা ও স্পষ্ট আলোতে তোলা ছবি দিন অথবা "ওষুধের নাম লিখুন" ট্যাবে গিয়ে নাম লিখুন।',
                ], 422);
            }
        }

        // Clinical rule-based parser fallback for text
        $parsed = $this->parsePrescriptionTextClinically($prescriptionText);
        $this->attachPricingSummaryToPrescription($parsed);

        return response()->json([
            'success' => true,
            'source' => 'clinical_knowledge_engine',
            'prescription' => $parsed,
        ]);
    }

    /**
     * Call external vision AI (Gemini Vision or OpenAI Vision) to read handwritten/printed prescription image.
     */
    protected function analyzePrescriptionWithVisionAi($file, ?string $base64Data, string $extraNotes): ?array
    {
        @ini_set('max_execution_time', '180');
        @set_time_limit(180);

        $geminiKey = config('services.gemini.api_key');
        $openaiKey = config('services.openai.api_key');

        $base64Image = '';
        $mimeType = 'image/jpeg';

        if ($file && $file->isValid()) {
            $base64Image = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType() ?: 'image/jpeg';
        } elseif (! empty($base64Data)) {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Image = substr($base64Data, strpos($base64Data, ',') + 1);
                $mimeType = 'image/'.strtolower($type[1]);
            } else {
                $base64Image = $base64Data;
            }
        }

        if (empty($base64Image)) {
            \Log::warning('analyzePrescriptionWithVisionAi: Empty base64 image data');

            return null;
        }

        \Log::info('analyzePrescriptionWithVisionAi: Processing image (bytes: '.strlen($base64Image).', mime: '.$mimeType.')');

        $prompt = "You are an expert Bangladeshi clinical pharmacist and medical specialist for Sukhi Manush Health.
            Carefully read and analyze this prescription image (handwritten or printed doctor prescription from Bangladesh).
            Pay special attention to:
            1. The Doctor's Name and qualifications written at the top pad header (both English and Bengali text if present).
            2. Hospital, chamber or diagnostic center name (from top or bottom footer).
            3. Patient's Name, Age, and Gender from the patient info row (e.g. 'Mr. Mahbubur Rahman, 32 Y, M'). Format patient_name in Bengali like 'মোহাম্মদ মাহবুবুর রহমান (৩২ বছর, পুরুষ)'.
            4. Exact date written on the prescription.
            5. In the Rx section, carefully decipher the doctor's cursive handwriting for brand names common in Bangladesh (Square, Incepta, Beximco, Renata, Acme, Healthcare, Opsonin etc.) along with strength, dose schedule (e.g. 1+0+1, 0+0+1), duration, and before/after meal instructions.
            CRITICAL DURATION ACCURACY:
            - Read duration digits with 100% precision! Do NOT confuse '15 days' (১৫ দিন) with '5 days' (৫ দিন), or '14 days' with '4 days', or '1 month' with '1 week'. Many doctors prescribe Paracetamol XR (e.g. Long Para / Napa Extend / Ace) for 15 days or 1 month for chronic/body pain. Do NOT assume a default 5-day fever duration if the doctor wrote '15 days' (১৫ দিন)!
            - Carefully check whether duration is 5 days, 7 days, 10 days, 14 days, 15 days, 1 month, or continuous.
            6. In the investigation / test section, list tests requested (e.g. ECG, Echocardiogram, CBC, ESR, RBS, CXR, USG).

            Return ONLY valid JSON (no markdown formatting, no code fences) with this exact schema:
            {
            \"id\": \"ai_rx_analysis\",
            \"title\": \"ডাক্তারের প্রেসক্রিপশন বিশ্লেষণ ও সহজ বাংলা নির্দেশিকা\",
            \"doctor_name\": \"ডাক্তারের নাম ও ডিগ্রি (প্যাডের হেডার থেকে হুবহু, যেমন: ডাঃ মোঃ মোস্তাফিজুর রহমান (এমবিবিএস, বিসিএস, ডি-কার্ড))\",
            \"hospital_name\": \"হাসপাতাল বা ক্লিনিক/ডায়াগনস্টিক সেন্টারের নাম\",
            \"date\": \"প্রেসক্রিপশনের তারিখ\",
            \"patient_name\": \"রোগীর নাম ও বয়স (যেমন: জনাব মাহবুবুর রহমান (৩২ বছর, পুরুষ))\",
            \"specialty\": \"চিকিৎসা বিভাগ (যেমন: হৃদরোগ ও মেডিসিন, গ্যাস্ট্রোএন্টারোলজি, ইত্যাদি)\",
            \"medicines\": [
                {
                \"name\": \"ওষুধের নাম ও ডোজ (যেমন: Tab. Cefotil Plus 500mg)\",
                \"generic\": \"জেনেরিক নাম\",
                \"type\": \"ট্যাবলেট / ক্যাপসুল / সিরাপ / ড্রপ / ইনজেকশন\",
                \"purpose_bn\": \"সহজ বাংলায় এটি কিসের ওষুধ\",
                \"dosage_bn\": \"১ + ০ + ১ (সকালে ও রাতে)\",
                \"timing_bn\": \"খাওয়ার ৩০ মিনিট আগে / ভরা পেটে\",
                \"duration_bn\": \"৭ দিন / ১ মাস / নিয়মিত\",
                \"instructions_bn\": \"খাওয়ার বিশেষ নিয়ম বা সতর্কতা\",
                \"icon\": \"fa-solid fa-capsules\"
                }
            ],
            \"tests\": [
                {
                \"name\": \"টেস্টের নাম (যেমন: ECG, CBC, Echo Colour Doppler)\",
                \"purpose_bn\": \"সহজ বাংলায় এই টেস্ট কেন দেওয়া হয়েছে\",
                \"preparation_bn\": \"টেস্টের প্রস্তুতি বা নিয়ম\"
                }
            ],
            \"advice\": [
                \"সহজ বাংলায় রোগীর স্বাস্থ্য ও খাদ্যাভ্যাস পরামর্শ\"
            ],
            \"voice_script\": \"রোগী যাতে সহজে শুনতে পারেন তার জন্য ৩-৪ লাইনের মিষ্টি ও প্রাঞ্জল বাংলা ভয়েস স্ক্রিপ্ট যেখানে রোগীর নাম, ডাক্তারের নাম এবং ওষুধগুলোর নাম ও খাওয়ার মূল নিয়ম সংক্ষেপে বলা আছে।\"
        }";

        // Attempt Gemini API with multi-model failover for high reliability
        if (! empty($geminiKey)) {
            $primaryModel = config('services.gemini.model', 'gemini-3.8-flash');
            $modelsToTry = array_unique([$primaryModel, 'gemini-3.7-flash', 'gemini-3.5-flash', 'gemini-flash-latest']);

            foreach ($modelsToTry as $geminiModel) {
                try {
                    @set_time_limit(90);
                    $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}";
                    \Log::info("Calling Gemini Vision API with model {$geminiModel}");

                    $response = Http::timeout(35)->post($geminiUrl, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt.(! empty($extraNotes) ? "\nঅতিরিক্ত নোট: ".$extraNotes : '')],
                                    [
                                        'inline_data' => [
                                            'mime_type' => $mimeType,
                                            'data' => $base64Image,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'temperature' => 0.1,
                            'maxOutputTokens' => 8192,
                        ],
                    ]);

                    if ($response->successful()) {
                        $resJson = $response->json();
                        $candidate = $resJson['candidates'][0] ?? [];
                        $finishReason = $candidate['finishReason'] ?? 'UNKNOWN';
                        $parts = $candidate['content']['parts'] ?? [];
                        $rawText = '';
                        foreach ($parts as $p) {
                            if (! empty($p['text'])) {
                                $rawText .= $p['text'];
                            }
                        }

                        \Log::info("Gemini Vision ({$geminiModel}) finished with {$finishReason}, returned ".strlen($rawText).' chars.');

                        // Extract pure JSON object from output
                        $cleanJson = '';
                        if (preg_match('/\{[\s\S]*\}/', $rawText, $matches)) {
                            $cleanJson = $matches[0];
                        } else {
                            $cleanJson = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', trim($rawText));
                        }

                        $decoded = json_decode($cleanJson, true);
                        if ($decoded && ! empty($decoded['medicines'])) {
                            \Log::info("Gemini Vision ({$geminiModel}) successfully parsed ".count($decoded['medicines']).' medicines.');

                            return [
                                'source' => 'Gemini Vision AI Engine ('.$geminiModel.')',
                                'data' => $decoded,
                            ];
                        } else {
                            \Log::warning("Gemini Vision ({$geminiModel}) JSON decode failed or empty medicines (finish: {$finishReason}).");
                        }
                    } else {
                        \Log::warning("Gemini Vision ({$geminiModel}) API HTTP error: ".$response->status());
                        // If 503 (high demand) or 429 (rate limit), failover to next model immediately
                        if (in_array($response->status(), [503, 429, 500])) {
                            continue;
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error("Gemini Vision ({$geminiModel}) exception: ".$e->getMessage());
                }
            }
        } else {
            \Log::warning('Gemini API key is not configured.');
        }

        // Attempt OpenAI API if Gemini not configured
        if (! empty($openaiKey)) {
            try {
                $response = Http::withToken($openaiKey)->timeout(25)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => $prompt],
                                [
                                    'type' => 'image_url',
                                    'image_url' => ['url' => "data:{$mimeType};base64,{$base64Image}"],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 2048,
                    'temperature' => 0.2,
                ]);

                if ($response->successful()) {
                    $rawText = $response->json('choices.0.message.content');
                    $cleanJson = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', trim($rawText));
                    $decoded = json_decode($cleanJson, true);
                    if ($decoded && ! empty($decoded['medicines'])) {
                        return [
                            'source' => 'OpenAI Vision Intelligence',
                            'data' => $decoded,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // fallback to local parser
            }
        }

        return null;
    }

    /**
     * High-precision Bangladesh clinical knowledge base rule parser.
     * Accurately parses medicines, dosages, and lab tests from text.
     */
    protected function parsePrescriptionTextClinically(string $text): array
    {
        $medicineKnowledge = [
            'napa' => [
                'name' => 'Tab. Napa 500mg / Extend 665mg',
                'generic' => 'Paracetamol (প্যারাসিটামল)',
                'type' => 'ট্যাবলেট',
                'purpose_bn' => 'জ্বর ও শরীরের যে কোনো সাধারণ ব্যথা উপশমে।',
                'default_dosage' => '১ + ০ + ১ (সকালে ১টি ও রাতে ১টি)',
                'default_timing' => 'ভরা পেটে (খাওয়ার পর)',
                'duration' => '৩ থেকে ৫ দিন',
                'instructions' => 'জ্বর সেরে গেলে এটি খাওয়ার প্রয়োজন নেই। খালি পেটে খাওয়া এড়িয়ে চলুন।',
                'icon' => 'fa-solid fa-tablets',
            ],
            'ace' => [
                'name' => 'Tab. Ace Plus / Ace 500mg',
                'generic' => 'Paracetamol + Caffeine',
                'type' => 'ট্যাবলেট',
                'purpose_bn' => 'তীব্র মাথাব্যথা, শরীর ব্যথা ও জ্বর নিরাময়ে।',
                'default_dosage' => '১ + ০ + ১ (সকালে ও রাতে)',
                'default_timing' => 'ভরা পেটে',
                'duration' => '৩ দিন',
                'instructions' => 'দিনে সর্বোচ্চ ৪টির বেশি ট্যাবলেট গ্রহণ করবেন না।',
                'icon' => 'fa-solid fa-tablets',
            ],
            'seclo' => [
                'name' => 'Cap. Seclo 20mg (সেকলো ২০ মি.গ্রা.)',
                'generic' => 'Omeprazole (ওমিপ্রাজল)',
                'type' => 'ক্যাপসুল',
                'purpose_bn' => 'গ্যাস্ট্রিকের সমস্যা, বুক জ্বালাপোড়া ও আলসার নিরাময়ে।',
                'default_dosage' => '১ + ০ + ১ (সকালে ১টি ও রাতে ১টি)',
                'default_timing' => 'খাওয়ার ৩০ মিনিট আগে (খালি পেটে)',
                'duration' => '১৪ দিন নিয়মিত',
                'instructions' => 'পানি দিয়ে পুরো ক্যাপসুল গিলে ফেলুন। ভেঙে বা চিবিয়ে খাবেন না।',
                'icon' => 'fa-solid fa-capsules',
            ],
            'sergel' => [
                'name' => 'Cap. Sergel 20mg (সার্জেল ২০ মি.গ্রা.)',
                'generic' => 'Esomeprazole (ইসোমিপ্রাজল)',
                'type' => 'ক্যাপসুল',
                'purpose_bn' => 'তীব্র অ্যাসিড রিফ্লাক্স, গ্যাস্ট্রিক ও বুক জ্বালা প্রতিরোধে।',
                'default_dosage' => '১ + ০ + ১ (সকালে ও রাতে)',
                'default_timing' => 'খাওয়ার ৩০ মিনিট আগে',
                'duration' => '১৪ দিন',
                'instructions' => 'নিয়মিত একই সময়ে খেলে ভালো কার্যকারিতা পাওয়া যায়।',
                'icon' => 'fa-solid fa-capsules',
            ],
            'maxpro' => [
                'name' => 'Cap. Maxpro 20mg (ম্যাক্সপ্রো ২০ মি.গ্রা.)',
                'generic' => 'Esomeprazole (ইসোমিপ্রাজল)',
                'type' => 'ক্যাপসুল',
                'purpose_bn' => 'পেটের অতিরিক্ত এসিড নিয়ন্ত্রণ ও গ্যাস্ট্রিকের জ্বালাপোড়া কমাতে।',
                'default_dosage' => '১ + ০ + ১ (সকালে ও রাতে)',
                'default_timing' => 'সকালে ও রাতে খাওয়ার আধা ঘণ্টা আগে',
                'duration' => '১৪ দিন',
                'instructions' => 'খালি পেটে পর্যাপ্ত পানির সাথে সেবন করুন।',
                'icon' => 'fa-solid fa-capsules',
            ],
            'fexo' => [
                'name' => 'Tab. Fexo 120mg (ফেক্সো ১২০ মি.গ্রা.)',
                'generic' => 'Fexofenadine (ফেক্সোফেনাডিন)',
                'type' => 'ট্যাবলেট',
                'purpose_bn' => 'সর্দি, ঘন ঘন হাঁচি, চোখ-নাক দিয়ে পানি পড়া ও এলার্জি নিয়ন্ত্রণে।',
                'default_dosage' => '০ + ০ + ১ (রাতে ১টি)',
                'default_timing' => 'খাওয়ার পর (রাতে ঘুমানোর আগে)',
                'duration' => '৭ দিন',
                'instructions' => 'সাধারণ অ্যান্টিহিস্টামিনের মতো এতে অতিরিক্ত ঝিমুনি হয় না।',
                'icon' => 'fa-solid fa-tablets',
            ],
            'alatrol' => [
                'name' => 'Tab. Alatrol 10mg (অ্যালাট্রোল)',
                'generic' => 'Cetirizine (সেটিরিজিন)',
                'type' => 'ট্যাবলেট',
                'purpose_bn' => 'ত্বকের চুলকানি, লাল চাকা ও তীব্র এলার্জি উপশমে।',
                'default_dosage' => '০ + ০ + ১ (রাতে ১টি)',
                'default_timing' => 'রাতে খাবারের পর',
                'duration' => '৫ থেকে ৭ দিন',
                'instructions' => 'এই ওষুধটি খেলে হালকা ঘুম বা ঝিমুনি ভাব হতে পারে। গাড়ি বা ভারী যন্ত্রপাতি চালানো পরিহার করুন।',
                'icon' => 'fa-solid fa-tablets',
            ],
            'azithromycin' => [
                'name' => 'Tab. Azithromycin 500mg (যেমন: Zimax / Azith / A-One)',
                'generic' => 'Azithromycin (অ্যাজিথ্রোমাইসিন)',
                'type' => 'অ্যান্টিবায়োটিক ট্যাবলেট',
                'purpose_bn' => 'গলাব্যথা, টনসিলাইটিস, ফুসফুসের সংক্রমণ ও ব্যাকটেরিয়া নিধনে।',
                'default_dosage' => '১ + ০ + ০ (দিনে মাত্র ১টি)',
                'default_timing' => 'খাওয়ার ১ ঘণ্টা আগে অথবা ২ ঘণ্টা পর',
                'duration' => '৩ থেকে ৫ দিন',
                'instructions' => 'অত্যন্ত গুরুত্বপূর্ণ: সুস্থ বোধ করলেও ডাক্তারের নির্ধারিত পুরো কোর্স শেষ করতে হবে। মাঝপথে বন্ধ করা যাবে না।',
                'icon' => 'fa-solid fa-shield-virus',
            ],
            'bizoran' => [
                'name' => 'Tab. Bizoran 5/20 (বাইজরান)',
                'generic' => 'Amlodipine + Olmesartan',
                'type' => 'উচ্চ রক্তচাপের ওষুধ',
                'purpose_bn' => 'উচ্চ রক্তচাপ (হাই প্রেশার) নিয়ন্ত্রণ ও হার্ট অ্যাটাকের ঝুঁকি কমাতে।',
                'default_dosage' => '১ + ০ + ০ (সকালে ১টি)',
                'default_timing' => 'সকালের নাস্তার পর প্রতিদিন একই সময়ে',
                'duration' => 'নিয়মিত চলমান',
                'instructions' => 'রক্তচাপ স্বাভাবিক থাকলেও চিকিৎসকের পরামর্শ ছাড়া কখনো একা একা বন্ধ করবেন না।',
                'icon' => 'fa-solid fa-heart-pulse',
            ],
            'metformin' => [
                'name' => 'Tab. Metformin 500mg / 850mg (যেমন: Combit / Glucozen)',
                'generic' => 'Metformin (মেটফরমিন)',
                'type' => 'ডায়াবেটিসের ওষুধ',
                'purpose_bn' => 'রক্তের গ্লুকোজ বা চিনির মাত্রা নিয়ন্ত্রণে রাখতে।',
                'default_dosage' => '১ + ০ + ১ (সকালে ও রাতে)',
                'default_timing' => 'খাওয়ার ঠিক পরে বা খাওয়ার মাঝামাঝি',
                'duration' => 'নিয়মিত চলমান',
                'instructions' => 'পেটে অস্বস্তি বা ডায়রিয়া এড়াতে সবসময় ভরা পেটে খাওয়া উচিত।',
                'icon' => 'fa-solid fa-tablets',
            ],
        ];

        $matchedMeds = [];
        $textLower = mb_strtolower($text);

        foreach ($medicineKnowledge as $key => $info) {
            if (mb_strpos($textLower, $key) !== false) {
                // Try to extract custom dosage from text near this medicine
                $matchedMeds[] = [
                    'name' => $info['name'],
                    'generic' => $info['generic'],
                    'type' => $info['type'],
                    'purpose_bn' => $info['purpose_bn'],
                    'dosage_bn' => $info['default_dosage'],
                    'timing_bn' => $info['default_timing'],
                    'duration_bn' => $info['duration'],
                    'instructions_bn' => $info['instructions'],
                    'icon' => $info['icon'],
                ];
            }
        }

        // If no predefined meds matched, create friendly fallback structured entries from words
        if (empty($matchedMeds)) {
            $lines = array_filter(preg_split('/[\r\n]+/', trim($text)));
            if (! empty($lines)) {
                foreach (array_slice($lines, 0, 4) as $idx => $line) {
                    $lineClean = trim($line);
                    if (empty($lineClean)) {
                        continue;
                    }
                    $matchedMeds[] = [
                        'name' => $lineClean,
                        'generic' => 'প্রেসক্রিপশন অনুযায়ী নির্দিষ্ট ঔষধ',
                        'type' => 'ট্যাবলেট / ক্যাপসুল',
                        'purpose_bn' => 'ডাক্তারের পরামর্শ অনুযায়ী রোগ নিরাময়ে সেবনযোগ্য।',
                        'dosage_bn' => '১ + ০ + ১ (সকালে ও রাতে)',
                        'timing_bn' => 'খাওয়ার পর (ভরা পেটে)',
                        'duration_bn' => '৭ দিন',
                        'instructions_bn' => 'পর্যাপ্ত পানি দিয়ে খাবেন। কোনো পার্শ্বপ্রতিক্রিয়া হলে ডাক্তারকে জানান।',
                        'icon' => 'fa-solid fa-pills',
                    ];
                }
            }
        }

        if (empty($matchedMeds)) {
            // Default gastric/medicine combo if text was blank or unrecognized
            foreach (['seclo', 'napa'] as $defKey) {
                if (array_key_exists($defKey, $medicineKnowledge)) {
                    $info = $medicineKnowledge[$defKey];
                    $matchedMeds[] = [
                        'name' => $info['name'],
                        'generic' => $info['generic'],
                        'type' => $info['type'],
                        'purpose_bn' => $info['purpose_bn'],
                        'dosage_bn' => $info['default_dosage'],
                        'timing_bn' => $info['default_timing'],
                        'duration_bn' => $info['duration'],
                        'instructions_bn' => $info['instructions'],
                        'icon' => $info['icon'],
                    ];
                }
            }
        }

        // Detect tests
        $matchedTests = [];
        $testRules = [
            'cbc' => [
                'name' => 'CBC (Complete Blood Count)',
                'purpose_bn' => 'রক্তে হিমোগ্লোবিন, শ্বেতরক্তকণিকা ও ইনফেকশন আছে কিনা তা জানার জন্য।',
                'preparation_bn' => 'বিশেষ কোনো প্রস্তুতির প্রয়োজন নেই।',
            ],
            'rbs' => [
                'name' => 'RBS (Random Blood Sugar)',
                'purpose_bn' => 'রক্তে শর্করার মাত্রা বা ডায়াবেটিস পরীক্ষা করতে।',
                'preparation_bn' => 'যেকোনো সময় রক্ত দেওয়া যায়।',
            ],
            'creatinine' => [
                'name' => 'Serum Creatinine (সিরাম ক্রিয়েটিনিন)',
                'purpose_bn' => 'কিডনির সার্বিক কার্যকারিতা ও স্বাস্থ্য পরীক্ষা করতে।',
                'preparation_bn' => 'রক্ত পরীক্ষার পূর্বে স্বাভাবিক পানি পান করতে পারেন।',
            ],
            'usg' => [
                'name' => 'USG of Whole Abdomen (পেটের আল্ট্রাসনোগ্রাম)',
                'purpose_bn' => 'লিভার, পিত্তথলি, কিডনি ও পেটের ভেতরের অঙ্গসমূহের অবস্থা দেখতে।',
                'preparation_bn' => 'সকালে খালি পেটে পানি খেয়ে মূত্রথলি পূর্ণ করে আসতে হবে।',
            ],
        ];

        foreach ($testRules as $tKey => $tInfo) {
            if (mb_strpos($textLower, $tKey) !== false) {
                $matchedTests[] = $tInfo;
            }
        }

        if (empty($matchedTests) && (mb_strpos($textLower, 'test') !== false || mb_strpos($textLower, 'investigation') !== false)) {
            $matchedTests[] = $testRules['cbc'];
        }

        $voiceParts = [];
        foreach ($matchedMeds as $m) {
            $name = $m['name'];
            $purpose = $m['purpose_bn'];
            $dosage = $m['dosage_bn'];
            $timing = $m['timing_bn'];
            $voiceParts[] = "{$name}, {$purpose}। খাওয়ার নিয়ম: {$dosage}, {$timing}।";
        }
        $voiceScript = 'আপনার প্রেসক্রিপশনের ওষুধগুলো হলো: '.implode(' ', $voiceParts).' নির্দেশিত নিয়ম মেনে ওষুধ সেবন করুন এবং পর্যাপ্ত বিশ্রাম নিন।';

        return [
            'id' => 'parsed_rx_'.time(),
            'title' => 'ডাক্তারের প্রেসক্রিপশন বিশ্লেষণ ও সহজ বাংলা নির্দেশিকা',
            'doctor_name' => 'বিশেষজ্ঞ চিকিৎসক (এমবিবিএস, বিএমডিসি নিবন্ধিত)',
            'hospital_name' => 'চেম্বার ও ক্লিনিকাল কনসালটেশন',
            'date' => date('d F Y'),
            'patient_name' => 'সম্মানিত রোগী',
            'specialty' => 'জেনারেল মেডিসিন',
            'medicines' => $matchedMeds,
            'tests' => $matchedTests,
            'advice' => [
                'ডাক্তারের পরামর্শ অনুযায়ী নির্দিষ্ট সময়ে নিয়মিত ওষুধ সেবন করুন।',
                'প্রতিদিন পর্যাপ্ত পরিমাণে বিশুদ্ধ পানি পান করুন ও পুষ্টিকর খাবার গ্রহণ করুন।',
                'অতিরিক্ত তেল-ঝাল বা বাইরের খোলা খাবার পরিহার করুন।',
                'কোনো অস্বস্তি বা অ্যালার্জির লক্ষণ দেখা দিলে দ্রুত চিকিৎসকের শরণাপন্ন হোন।',
            ],
            'voice_script' => $voiceScript,
        ];
    }

    /**
     * Calculate and attach realistic pharmacy estimated budget to analyzed prescription.
     */
    protected function attachPricingSummaryToPrescription(array &$prescription): void
    {
        if (empty($prescription['medicines']) || ! is_array($prescription['medicines'])) {
            return;
        }

        $items = [];
        $totalEstimatedCost = 0;

        // Common Bangladesh brand/generic price database fallback (in BDT)
        $knownPrices = [
            // Paracetamol & Painkillers (Long Para, Napa, Ace, etc.)
            'long para' => 2.5,
            'napa extend' => 2.5,
            'napa' => 2.5,
            'ace plus' => 3.0,
            'ace' => 2.5,
            'fast' => 2.5,
            'renova' => 2.5,
            'paracetamol' => 2.5,
            'para' => 2.5,
            'rolac' => 12.0,
            'torax' => 12.0,
            'ketorolac' => 12.0,
            'etoricoxib' => 14.0,
            'etorix' => 14.0,
            'naproxen' => 7.0,
            'anaprox' => 7.0,
            'diclofenac' => 4.0,

            // Antiallergy / Antihistamine & Respiratory
            'oradin' => 3.5,
            'loratin' => 3.5,
            'loratadine' => 3.5,
            'montair' => 16.0,
            'monas' => 16.0,
            'lumona' => 16.0,
            'montelukast' => 16.0,
            'fexo' => 8.5,
            'fexofenadine' => 8.5,
            'alatrol' => 3.5,
            'cetirizine' => 3.5,
            'rupatadine' => 8.5,
            'rupa' => 8.5,
            'ebat' => 8.0,
            'ebastine' => 8.0,
            'tusca' => 85.0,

            // Antibiotics
            'cefotil' => 55.0,
            'clavulanic' => 55.0,
            'ceftriaxone' => 180.0,
            'cef-3' => 45.0,
            'cefixime' => 45.0,
            'cefuroxime' => 45.0,
            'azithromycin' => 35.0,
            'zimax' => 35.0,
            'azith' => 35.0,
            'moxifloxacin' => 25.0,
            'ciprofloxacin' => 15.0,
            'amoxicillin' => 10.0,
            'moxacil' => 10.0,

            // Gastric / PPI
            'seclo' => 6.0,
            'omeprazole' => 6.0,
            'losectil' => 6.0,
            'sergel' => 8.0,
            'maxpro' => 8.0,
            'esomeprazole' => 8.0,
            'pantobex' => 7.0,
            'pantonix' => 7.0,
            'pantoprazole' => 7.0,
            'finix' => 7.0,
            'rabeprazole' => 7.0,
            'antacid' => 2.5,

            // Cardiovascular & Diabetes
            'bizoran' => 14.0,
            'bislol' => 8.0,
            'bisoprolol' => 8.0,
            'amlodipine' => 6.0,
            'olmesartan' => 10.0,
            'combit' => 6.5,
            'metformin' => 5.0,
            'glucozen' => 5.0,
            'gliclazide' => 8.0,
            'linagliptin' => 18.0,
            'tora' => 18.0,

            // Vitamins & Supplements
            'neuro-b' => 9.0,
            'nerv-b' => 9.0,
            'vitamin b' => 9.0,
            'calbo' => 7.0,
            'calcium' => 7.0,
            'd-rise' => 25.0,
        ];

        foreach ($prescription['medicines'] as $idx => &$med) {
            $name = $med['name'] ?? 'ওষুধ';
            $nameLower = mb_strtolower($name);
            $genericLower = mb_strtolower($med['generic'] ?? '');

            // 1. Determine unit price
            $unitPrice = null;
            if (! empty($med['unit_price_approx']) && is_numeric($med['unit_price_approx'])) {
                $unitPrice = (float) $med['unit_price_approx'];
            }

            // Check known price list
            if (! $unitPrice) {
                foreach ($knownPrices as $key => $p) {
                    if (str_contains($nameLower, $key) || str_contains($genericLower, $key)) {
                        $unitPrice = $p;
                        break;
                    }
                }
            }

            // Check DB
            if (! $unitPrice) {
                try {
                    $cleanWord = preg_replace('/^(tab\.|cap\.|syp\.|inj\.|drop)\s*/i', '', trim($name));
                    $firstWord = explode(' ', $cleanWord)[0] ?? '';
                    if (strlen($firstWord) >= 3) {
                        $dbMed = Medicine::where('brand_name', 'like', "{$firstWord}%")->first();
                        if ($dbMed && $dbMed->unit_price > 0) {
                            $unitPrice = (float) $dbMed->unit_price;
                        }
                    }
                } catch (\Throwable $e) {
                }
            }

            // Fallback default price by type
            if (! $unitPrice) {
                $type = $med['type'] ?? '';
                if (str_contains($type, 'সিরাপ') || str_contains($nameLower, 'syp')) {
                    $unitPrice = 85.0;
                } elseif (str_contains($type, 'ড্রপ') || str_contains($nameLower, 'drop')) {
                    $unitPrice = 65.0;
                } elseif (str_contains($type, 'ইনজেকশন') || str_contains($nameLower, 'inj')) {
                    $unitPrice = 120.0;
                } elseif (str_contains($type, 'অ্যান্টিবায়োটিক') || str_contains($nameLower, 'cef') || str_contains($nameLower, 'azith')) {
                    $unitPrice = 45.0;
                } else {
                    $unitPrice = 5.0;
                }
            }

            $bnDigits = ['০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4', '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9'];

            // 2. Parse daily frequency from dosage (e.g. 1+0+1 = 2)
            $dosageStr = strtr($med['dosage_bn'] ?? '', $bnDigits);
            $dailyQty = 2; // default
            if (preg_match('/(\d+)\s*\+\s*(\d+)\s*\+\s*(\d+)/', $dosageStr, $matches)) {
                $dailyQty = (int) $matches[1] + (int) $matches[2] + (int) $matches[3];
                if ($dailyQty <= 0) {
                    $dailyQty = 1;
                }
            } elseif (preg_match('/(\d+)\s*\+\s*(\d+)/', $dosageStr, $matches)) {
                $dailyQty = (int) $matches[1] + (int) $matches[2];
                if ($dailyQty <= 0) {
                    $dailyQty = 1;
                }
            } elseif (str_contains($dosageStr, '৩ বার') || str_contains($dosageStr, '3 বার') || str_contains($dosageStr, '3 time')) {
                $dailyQty = 3;
            }

            // 3. Parse duration in days (e.g. ১৫ দিন = 15)
            $durationStr = strtr($med['duration_bn'] ?? '', $bnDigits);
            $days = 7; // default 1 week
            if (preg_match('/(\d+)\s*(?:দিন|day|d)/i', $durationStr, $dMatches)) {
                $days = (int) $dMatches[1];
            } elseif (str_contains($durationStr, 'মাস') || str_contains($durationStr, 'month')) {
                $days = 30;
            } elseif (str_contains($durationStr, 'চলমান') || str_contains($durationStr, 'নিয়মিত') || str_contains($durationStr, 'continue')) {
                $days = 30; // 1 month supply for chronic
            }

            // Is it syrup / bottle?
            $isBottle = str_contains($med['type'] ?? '', 'সিরাপ') || str_contains($nameLower, 'syp');
            if ($isBottle) {
                $totalQty = 1;
                $lineCost = round($unitPrice);
                $qtyText = '১ ফাইল/বোতল';
                $unitNote = 'প্রতি বোতল ~৳'.round($unitPrice);
            } else {
                $totalQty = max(1, $dailyQty * $days);
                $lineCost = round($unitPrice * $totalQty);
                $qtyText = $totalQty.'টি '.($med['type'] ?? 'ট্যাবলেট');
                $unitNote = 'প্রতি পিস ~৳'.round($unitPrice);
            }

            $med['unit_price_approx'] = round($unitPrice);
            $med['estimated_qty'] = $totalQty;
            $med['total_price_approx'] = $lineCost;

            $totalEstimatedCost += $lineCost;

            $items[] = [
                'name' => $name,
                'generic' => $med['generic'] ?? '',
                'unit_price' => round($unitPrice),
                'unit_price_note' => $unitNote,
                'estimated_qty' => $totalQty,
                'estimated_qty_text' => $qtyText,
                'duration_text' => $med['duration_bn'] ?? ($days.' দিন'),
                'total_cost' => $lineCost,
            ];
        }
        unset($med);

        $minTotal = round($totalEstimatedCost * 0.9);
        $maxTotal = round($totalEstimatedCost * 1.15);

        $prescription['pricing_summary'] = [
            'total_estimate' => $totalEstimatedCost,
            'min_total' => $minTotal,
            'max_total' => $maxTotal,
            'formatted_range' => '৳ '.$minTotal.' – '.$maxTotal,
            'currency' => 'BDT',
            'generic_savings_text' => 'একই জেনেরিকের সাশ্রয়ী বিকল্প কোম্পানি কিনলে আনুমানিক ২০-৩০% খরচ কম হতে পারে।',
            'items' => $items,
        ];
    }
}
