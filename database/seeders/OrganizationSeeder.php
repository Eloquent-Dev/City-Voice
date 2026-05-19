<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Division;
use App\Models\Category;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizationTree = [
            'القطاع الصحي و الزراعي' => [
                'المراكز الصحية' => [
                    ['name' => 'أفراد-منازل/إحداث روائح او صوت تضر الصحة او الآخرين','allowance_period' => 4],
                    ['name' => 'أفراد-منازل/إفراغ مياه عادمة في اماكن غير مخصصة لها', 'allowance_period' => 4],
                    ['name'=> 'أفراد-منازل/تربية حيوانات تؤدي الى ضرر-عدا المرخص', 'allowance_period'=> 10],
                    ['name'=> 'أفراد-منازل/حفر او قنوات تتجمع فيها المياه العادمة', 'allowance_period'=> 6],
                    ['name'=> 'أفراد-منازل/طرح نفايات خارج الاماكن المخصصة', 'allowance_period'=> 4],
                    ['name'=> 'أفراد-منازل/عدم نظافة العقار المشغول او ملحقاته', 'allowance_period'=> 5],
                    ['name'=> 'أفراد-منازل/عمل يلحق الضرر بالصحة ويقلق الراحة', 'allowance_period'=> 4],
                    ['name'=> 'أفراد-منازل/شكاوي تدخين', 'allowance_period'=> 4],
                    ['name'=> 'أفراد-منازل/وجود مدخنة تلحق الضرر بالغير', 'allowance_period'=> 6],
                ],
                'مراكز الرقابة و الصحة' => [
                    ['name' => 'منشأة تجارية / عرض مواد غذائية على الرصيف','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / مخالفات اشتراطات صحية','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / ممارسة مهنة بدون ترخيص','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / مواد غذائية منتهية الصلاحية','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / إحداث حفر او قنوات تحتوي مياه عادمة','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / إحداث روائح او صوت تضر الصحة او الآخرين','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / إفراغ مياه عادمة في اماكن غير مخصصة لها','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / تربية حيوانات تؤدي الى ضرر-عدا المرخص','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / طرح نفايات خارج الاماكن المخصصة','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / عدم نظافة العقار المشغول او ملحقاته','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / عمل يلحق الضرر بالصحة و يقلق الراحة','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / شكاوي تدخين','allowance_period'=>1],
                    ['name' => 'منشأة تجارية / وجود مدخنة تلحق الضرر بالغير','allowance_period'=>1],
                ]
            ],
            'التخطيط' => [
                'دائرة المهن' => [
                    ['name' => 'شكوى على لوحة إعلانية', 'allowance_period'=>7]
                ]
            ],
            'الشؤون الإجتماعية' => [
                'دائرة المرافق و البرامج الإجتماعية'=> [
                    ['name'=> 'نظافة المكتبة', 'allowance_period'=>5],
                    ['name'=> 'صيانة مرافق رياضية', 'allowance_period'=>7]
                ]
            ],
            'الاشغال العامة' => [
                'دائرة الانشاءات' => [
                    ['name' => 'صيانة ادراج و اسوار','allowance_period'=>30],
                    ['name' => 'صيانة اطاريف و ارصفة','allowance_period'=>30],
                    ['name' => 'صيانة جدار استنادي','allowance_period'=>70]
                ],
                'دائرة صيانة الطرق' => [
                    ['name' => 'رفع و تنزيل و تركيب منهل','allowance_period'=>7],
                    ['name' => 'صيانة حفريات او هبوط','allowance_period'=>15],
                ]
            ],
            'مناطق امانة عمان' => [
                'قسم البيئة' => [
                    ['name' => 'نظافة شوارع','allowance_period'=>2],
                    ['name' => 'نظافة ساحات','allowance_period'=>3],
                    ['name' => 'تنظيف مناهل الامطار','allowance_period'=>3],
                    ['name' => 'إزالة المعرشات','allowance_period'=>7],
                    ['name' => 'حرق النفايات','allowance_period'=>2],
                    ['name' => 'تراكم النفايات','allowance_period'=>2],
                    ['name' => 'عدم مرور طاحنات','allowance_period'=>1],
                    ['name' => 'صيانة حاويات','allowance_period'=>5],
                    ['name' => 'وجود نفايات حول الحاويات','allowance_period'=>2],
                    ['name' => 'تجمع مياه الامطار','allowance_period'=>1],
                    ['name' => 'دخول الامطار الى المنازل','allowance_period'=>1],
                    ['name' => 'إزالة مخلفات تقليم الاشجار','allowance_period'=>3],
                ],
                'وحدة التطوير بالمنطقة'=> [
                    ['name' => 'شكوى على أداء المنطقة','allowance_period'=>7],
                ],
                'دائرة البيع العشوائي' => [
                    ['name' => 'إزالة البسطات', 'allowance_period'=>3]
                ],
                'قسم رقابة اعمار في المنطقة'=> [
                    ['name' => 'شكوى ابنية لا يمكن ترخيصها','allowance_period'=>30],
                    ['name' => 'وجود عوائق إنشائية في سعة الشارع','allowance_period'=>7],
                    ['name' => 'وجود مواد و مخلفات ابنية في سعة الشارع','allowance_period'=>7],
                    ['name' => 'قلابات تطرح طمم عشوائي','allowance_period'=>4],
                    ['name' => 'حفريات مخالفة','allowance_period'=>30],
                    ['name' => 'عدم توفر شروط السلامة العامة في المنشأة','allowance_period'=>5],
                    ['name' => 'حفريات مهجورة','allowance_period'=>30],
                    ['name' => 'تأثر منشأة مجاورة لحفريات','allowance_period'=>2],
                    ['name' => 'استمرار اعمال إنشائية لساعات متأخرة','allowance_period'=>3],
                    ['name' => 'قذف رملي','allowance_period'=>3],
                    ['name' => 'شكوى على تغيير صفة الاستعمال لجزء من بناء مهني','allowance_period'=>30],
                    ['name' => 'شكوى على تغيير صفة الاستعمال لجزء من بناء صحي','allowance_period'=>30],
                    ['name' => 'شكوى على حفرية بنية تحتية','allowance_period'=>3],
                    ['name' => 'شكاوي ابنية / منشآت مخالفة','allowance_period'=>30],
                    ['name' => 'حواجز اسمنتية و حديدية/ عوائق','allowance_period'=>7],
                    ['name' => 'إزالة مطبات اسمنتية عشوائية','allowance_period'=>7],
                    ['name' => 'استغلال رصيف/ عوائق','allowance_period'=>5],
                    ['name' => 'استغلال ارتداد امامي','allowance_period'=>21],
                ]
            ]
        ];

        foreach($organizationTree as $deptName => $divisions){
            $department = Department::firstOrCreate(['name' =>$deptName]);

            foreach($divisions as $divName => $categories){
                $division = Division::firstOrCreate([
                    'name' => $divName,
                    'dept_id' => $department->id
                ]);

                foreach($categories as $category){
                    Category::firstOrCreate([
                        'name' => $category['name'],
                        'allowance_period' => $category['allowance_period'],
                        'division_id' => $division->id
                    ]);
                }
            }
        }

        $this->command->info('Organization tree seeded successfully!');
    }
}
