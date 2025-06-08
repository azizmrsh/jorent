<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

require_once database_path('helpers/ArabicFakerHelper.php');

class TenantSeeder extends Seeder
{
    public function run()
    {
        // إنشاء مستأجرين مع بيانات أردنية واقعية
        $tenants = [
            [
                'firstname' => 'محمد',
                'midname' => 'عبدالله',
                'lastname' => 'الأحمد',
                'email' => 'mohammed.ahmad' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'عمان - جبل الحسين',
                'birth_date' => '1985-03-15',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'id',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(6),
                'hired_by' => 'أحمد السالم',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'firstname' => 'فاطمة',
                'midname' => 'سعد',
                'lastname' => 'الزهراني',
                'email' => 'fatima.zahrani' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'إربد - وسط البلد',
                'birth_date' => '1990-07-22',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'passport',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(3),
                'hired_by' => 'سارة الدوسري',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'firstname' => 'خالد',
                'midname' => 'عبدالرحمن',
                'lastname' => 'المجالي',
                'email' => 'khalid.majali' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'الزرقاء - حي الأمير راشد',
                'birth_date' => '1982-11-08',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'driver_license',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subYear(),
                'hired_by' => 'عبدالله النعيمي',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'firstname' => 'نورا',
                'midname' => 'فيصل',
                'lastname' => 'الشريف',
                'email' => 'nora.sharif' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'السلط - وسط البلد',
                'birth_date' => '1988-01-30',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'id',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(8),
                'hired_by' => 'مها الرشيد',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'firstname' => 'عبدالعزيز',
                'midname' => 'محمد',
                'lastname' => 'الطراونة',
                'email' => 'abdulaziz.tarawna' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'الكرك - حي الثقافة',
                'birth_date' => '1975-09-12',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'passport',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(18),
                'hired_by' => 'فهد المنصور',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'firstname' => 'أميرة',
                'midname' => 'عامر',
                'lastname' => 'البطاينة',
                'email' => 'amira.batayna' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'عجلون - الشفا',
                'birth_date' => '1993-05-18',
                'password' => Hash::make('password123'),
                'status' => 'unactive',
                'document_type' => 'id',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(2),
                'hired_by' => 'ليلى الفارس',
                'email_verified_at' => null, // غير مفعل البريد
            ],
            [
                'firstname' => 'يوسف',
                'midname' => 'أحمد',
                'lastname' => 'الخالدي',
                'email' => 'youssef.khalidi' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'جرش - وسط البلد',
                'birth_date' => '1980-12-25',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'passport',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(12),
                'hired_by' => 'سارة الحسيني',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'firstname' => 'ليلى',
                'midname' => 'سليم',
                'lastname' => 'العجلوني',
                'email' => 'layla.ajlouni' . rand(1000, 9999) . '@email.com',
                'phone' => '+96279' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => 'العقبة - النهضة',
                'birth_date' => '1987-04-14',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'document_type' => 'id',
                'document_number' => '1' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'nationality' => 'أردني',
                'hired_date' => Carbon::now()->subMonths(4),
                'hired_by' => 'كارلوس مارتينيز',
                'email_verified_at' => Carbon::now(),
            ]
        ];

        // إنشاء المستأجرين المحددين
        foreach ($tenants as $tenant) {
            Tenant::create($tenant);
        }

        // إنشاء 42 مستأجر إضافي باستخدام Factory ليصبح المجموع 50
        Tenant::factory(42)->create();

        $this->command->info('تم إنشاء ' . (count($tenants) + 42) . ' مستأجر بنجاح!');
        $this->command->info('البيانات تشمل:');
        $this->command->info('- ' . count($tenants) . ' مستأجرين بأسماء وبيانات أردنية واقعية');
        $this->command->info('- 42 مستأجر إضافي بأسماء عربية عشوائية');
        $this->command->info('- جميع البيانات من الأردن مع أرقام هواتف أردنية');
        $this->command->info('- أنواع مختلفة من الوثائق (هوية، جواز سفر، رخصة قيادة)');
        $this->command->info('- حالات مختلفة (نشط، غير نشط)');
    }
}
