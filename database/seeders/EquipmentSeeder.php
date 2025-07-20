<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $equipment = [
            // Shared Apartment Facilities - Furniture
            [
                'name_en'        => 'Chair',
                'name_ar'        => 'كرسي',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Living room chair.',
                'description_ar' => 'كرسي غرفة معيشة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Chair',
                'name_ar'        => 'كرسي',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Living room chair.',
                'description_ar' => 'كرسي غرفة معيشة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Cabinet',
                'name_ar'        => 'كابينت',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Living room cabinet.',
                'description_ar' => 'كابينت غرفة معيشة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Small Table',
                'name_ar'        => 'منضدة صغيرة',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Small table for living room.',
                'description_ar' => 'منضدة صغيرة لغرفة المعيشة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Large Table',
                'name_ar'        => 'منضدة كبيرة',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Large table for living room.',
                'description_ar' => 'منضدة كبيرة لغرفة المعيشة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Dining Table Set',
                'name_ar'        => 'منضدة سفرة مجهزة',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Dining table with 4 chairs.',
                'description_ar' => 'منضدة وعدد 4 كراسي.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Electrical Appliances
            [
                'name_en'        => 'Refrigerator',
                'name_ar'        => 'ثلاجة',
                'category_en'    => 'Electrical Appliances',
                'category_ar'    => 'أجهزة كهربائية',
                'description_en' => 'Refrigerator for food storage.',
                'description_ar' => 'ثلاجة لحفظ الطعام.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Automatic Washing Machine',
                'name_ar'        => 'غسالة فل أوتوماتيك',
                'category_en'    => 'Electrical Appliances',
                'category_ar'    => 'أجهزة كهربائية',
                'description_en' => 'Automatic washing machine for laundry.',
                'description_ar' => 'غسالة فل أوتوماتيك للغسيل.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Microwave',
                'name_ar'        => 'ميكرويف',
                'category_en'    => 'Electrical Appliances',
                'category_ar'    => 'أجهزة كهربائية',
                'description_en' => 'Microwave oven for heating food.',
                'description_ar' => 'ميكرويف لتسخين الطعام.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Water Heater (Kettle)',
                'name_ar'        => 'سخان ماء "كاتل"',
                'category_en'    => 'Electrical Appliances',
                'category_ar'    => 'أجهزة كهربائية',
                'description_en' => 'Water heater (kettle) for boiling water.',
                'description_ar' => 'سخان ماء "كاتل" لغلي الماء.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Electric Heater',
                'name_ar'        => 'سخان كهربائي',
                'category_en'    => 'Electrical Appliances',
                'category_ar'    => 'أجهزة كهربائية',
                'description_en' => 'Electric heater for heating the room.',
                'description_ar' => 'سخان كهربائي لتدفئة الغرفة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Home Decor
            [
                'name_en'        => 'Curtains',
                'name_ar'        => 'ستائر',
                'category_en'    => 'Home Decor',
                'category_ar'    => 'ديكور المنزل',
                'description_en' => 'Window curtains for privacy and decoration.',
                'description_ar' => 'ستائر للنوافذ للخصوصية والديكور.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Kitchen Items
            [
                'name_en'        => 'Wooden Kitchen Set',
                'name_ar'        => 'مطبخ خشب قطعتين',
                'category_en'    => 'Kitchen Items',
                'category_ar'    => 'مستلزمات المطبخ',
                'description_en' => 'Two-piece wooden kitchen set.',
                'description_ar' => 'مطبخ خشب قطعتين.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Student Room Features - Furniture
            [
                'name_en'        => 'Bed',
                'name_ar'        => 'سرير',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Student bed for sleeping.',
                'description_ar' => 'سرير للطالب للنوم.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Wardrobe',
                'name_ar'        => 'خزانة',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Wardrobe for storing clothes and personal items.',
                'description_ar' => 'خزانة لتخزين الملابس والأغراض الشخصية.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Study Desk',
                'name_ar'        => 'مكتب',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Study desk for academic work.',
                'description_ar' => 'مكتب للعمل الأكاديمي.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Desk Chair',
                'name_ar'        => 'كرسي مكتب',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Chair for the study desk.',
                'description_ar' => 'كرسي للمكتب الدراسي.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Shelf',
                'name_ar'        => 'دولاب',
                'category_en'    => 'Furniture',
                'category_ar'    => 'أثاث',
                'description_en' => 'Shelf for storing books and items.',
                'description_ar' => 'دولاب لتخزين الكتب والأغراض.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Electrical Appliances for Room
            [
                'name_en'        => 'Fan',
                'name_ar'        => 'مروحة',
                'category_en'    => 'Electrical Appliances',
                'category_ar'    => 'أجهزة كهربائية',
                'description_en' => 'Electric fan for cooling.',
                'description_ar' => 'مروحة كهربائية للتبريد.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Home Decor for Room
            [
                'name_en'        => 'Small Carpet',
                'name_ar'        => 'سجادة صغيرة',
                'category_en'    => 'Home Decor',
                'category_ar'    => 'ديكور المنزل',
                'description_en' => 'Small carpet for the room floor.',
                'description_ar' => 'سجادة صغيرة لأرضية الغرفة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Personal Items (Student must provide) - Kitchen Items
            [
                'name_en'        => 'Personal Kitchen Set',
                'name_ar'        => 'أطقم أطباق مكون من',
                'category_en'    => 'Kitchen Items',
                'category_ar'    => 'مستلزمات المطبخ',
                'description_en' => 'Personal kitchen set including: 2 plates, small spoon, large spoon, fork, knife, saucer, small spoon, large spoon.',
                'description_ar' => 'أطقم أطباق مكون من: 2 طبق، ملعقة صغيرة، ملعقة كبيرة، شوكة، سكينة، صحن، ملعقة صغيرة، ملعقة كبيرة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Personal Items (Student must provide) - Bedding
            [
                'name_en'        => 'Bedding Set',
                'name_ar'        => 'طقم ملايات',
                'category_en'    => 'Bedding',
                'category_ar'    => 'مستلزمات النوم',
                'description_en' => 'Bedding set including mattress, pillow, pillowcase, bed sheet set, duvet cover, mattress cover, duvet.',
                'description_ar' => 'مرتبة، مخدة، خدادية، طقم ملايات، غطاء لحاف، غطاء مرتبة، لحاف.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ];

        DB::table('equipment')->insert($equipment);
    }
}