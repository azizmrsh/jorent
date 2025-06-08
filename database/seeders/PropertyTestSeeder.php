<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Address;
use App\Models\Acc;

require_once database_path('helpers/ArabicFakerHelper.php');

class PropertyTestSeeder extends Seeder
{
    public function run()
    {
        // التأكد من وجود حساب
        $acc = Acc::first();
        if (!$acc) {
            $acc = Acc::factory()->create();
        }

        // إنشاء عناوين أردنية
        $address1 = Address::create([
            'country' => 'الأردن',
            'governorate' => 'عمان',
            'city' => 'عمان',
            'district' => 'العبدلي',
            'building_number' => '123',
            'plot_number' => 'قطعة-456',
            'basin_number' => 'حوض-12',
            'property_number' => 'عقار-7890',
            'street_name' => 'شارع الملك عبدالله الثاني'
        ]);

        $address2 = Address::create([
            'country' => 'الأردن',
            'governorate' => 'عمان', 
            'city' => 'عمان',
            'district' => 'وسط البلد',
            'building_number' => '456',
            'plot_number' => 'قطعة-789',
            'basin_number' => 'حوض-34',
            'property_number' => 'عقار-5678',
            'street_name' => 'شارع الرينبو'
        ]);

        $address3 = Address::create([
            'country' => 'الأردن',
            'governorate' => 'الزرقاء', 
            'city' => 'الزرقاء',
            'district' => 'الأمير راشد',
            'building_number' => '789',
            'plot_number' => 'قطعة-123',
            'basin_number' => 'حوض-56',
            'property_number' => 'عقار-9012',
            'street_name' => 'شارع الأمير حسن'
        ]);

        // إنشاء عقارات مع البيانات العربية
        Property::create([
            'name' => 'فيلا الياسمين الفاخرة',
            'description' => 'فيلا جميلة مع حديقة واسعة في منطقة راقية، تحتوي على جميع الخدمات الأساسية والكماليات',
            'type1' => 'villa',
            'type2' => 'residential',
            'features' => [
                'parking' => true,
                'security' => true,
                'gym' => false,
                'swimming_pool' => true,
                'elevator' => false,
                'garden' => true,
            ],
            'birth_date' => now()->subYears(5),
            'floors_count' => 3,
            'floor_area' => 200.0,
            'total_area' => 600.0,
            'address_id' => $address1->id,
            'acc_id' => $acc->id,
            'image_path' => 'uploads/properties/villa_yasmin.jpg'
        ]);

        Property::create([
            'name' => 'شقة العاصمة الحديثة',
            'description' => 'شقة عصرية في وسط عمان مع إطلالة رائعة، قريبة من جميع الخدمات والمرافق العامة',
            'type1' => 'building',
            'type2' => 'residential', 
            'features' => [
                'parking' => true,
                'security' => true,
                'gym' => true,
                'swimming_pool' => false,
                'elevator' => true,
                'garden' => false,
            ],
            'birth_date' => now()->subYears(2),
            'floors_count' => 1,
            'floor_area' => 120.0,
            'total_area' => 120.0,
            'address_id' => $address2->id,
            'acc_id' => $acc->id,
            'image_path' => 'uploads/properties/apartment_downtown.jpg'
        ]);

        Property::create([
            'name' => 'مجمع النخيل التجاري',
            'description' => 'مستودع تجاري واسع مناسب للشركات والمؤسسات التجارية مع مواقف سيارات كبيرة',
            'type1' => 'warehouse',
            'type2' => 'commercial',
            'features' => [
                'parking' => true,
                'security' => true,
                'gym' => false,
                'swimming_pool' => false,
                'elevator' => false,
                'garden' => false,
            ],
            'birth_date' => now()->subYears(8),
            'floors_count' => 1,
            'floor_area' => 800.0,
            'total_area' => 800.0,
            'address_id' => $address3->id,
            'acc_id' => $acc->id,
            'image_path' => 'uploads/properties/warehouse_nakheel.jpg'
        ]);
    }
}
