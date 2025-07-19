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
            // Shared Apartment Facilities
            [
                'name_en'        => 'Kitchen',
                'name_ar'        => 'مطبخ',
                'description_en' => 'A room or area where food is prepared and cooked.',
                'description_ar' => 'غرفة أو منطقة يتم فيها إعداد الطعام وطهيه.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Reception Hall',
                'name_ar'        => 'قاعة استقبال',
                'description_en' => 'A hall for receiving guests or visitors.',
                'description_ar' => 'قاعة لاستقبال الضيوف أو الزوار.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Living Room',
                'name_ar'        => 'غرفة معيشة',
                'description_en' => 'A room in a house for general and informal everyday use.',
                'description_ar' => 'غرفة في المنزل للاستخدام اليومي العام وغير الرسمي.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Dining Table with 4 Chairs',
                'name_ar'        => 'طاولة طعام مع 4 كراسي',
                'description_en' => 'A table and four chairs for eating meals.',
                'description_ar' => 'طاولة وأربع كراسي لتناول الوجبات.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => '10 kg Washing Machine',
                'name_ar'        => 'غسالة 10 كجم',
                'description_en' => 'A machine for washing clothes, with a 10 kg capacity.',
                'description_ar' => 'آلة لغسل الملابس بسعة 10 كجم.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Microwave',
                'name_ar'        => 'ميكروويف',
                'description_en' => 'An electric oven that heats and cooks food quickly.',
                'description_ar' => 'فرن كهربائي يسخن ويطبخ الطعام بسرعة.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Gas Stove (pre-installed, personal stoves not permitted)',
                'name_ar'        => 'موقد غاز (مثبت مسبقًا، لا يسمح بالمواقد الشخصية)',
                'description_en' => 'A pre-installed gas stove for cooking; personal stoves are not allowed.',
                'description_ar' => 'موقد غاز مثبت مسبقًا للطهي؛ لا يسمح بالمواقد الشخصية.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Iron (one per apartment)',
                'name_ar'        => 'مكواة (واحدة لكل شقة)',
                'description_en' => 'A device for pressing clothes to remove wrinkles.',
                'description_ar' => 'جهاز لكي الملابس وإزالة التجاعيد.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Student Room Features
            [
                'name_en'        => 'Single Occupancy (one student per room)',
                'name_ar'        => 'إشغال فردي (طالب واحد لكل غرفة)',
                'description_en' => 'Each room is occupied by only one student.',
                'description_ar' => 'كل غرفة يشغلها طالب واحد فقط.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Hotel-Style Furnishings',
                'name_ar'        => 'أثاث على طراز الفنادق',
                'description_en' => 'Furniture similar to that found in hotels.',
                'description_ar' => 'أثاث مشابه لذلك الموجود في الفنادق.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name_en'        => 'Fan',
                'name_ar'        => 'مروحة',
                'description_en' => 'A device for creating airflow and cooling.',
                'description_ar' => 'جهاز لتوليد تدفق الهواء والتبريد.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],

            // Personal Items
            [
                'name_en'        => 'Personal Items (mattress cover, pillow, blanket, etc.)',
                'name_ar'        => 'أدوات شخصية (غطاء مرتبة، وسادة، بطانية، إلخ)',
                'description_en' => 'Personal bedding and comfort items such as mattress cover, pillow, and blanket.',
                'description_ar' => 'مستلزمات شخصية للنوم والراحة مثل غطاء المرتبة، الوسادة، والبطانية.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ];

        DB::table('equipment')->insert($equipment);
    }
}