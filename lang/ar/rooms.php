<?php

return [
        'page_title' => 'إدارة الغرف',
        
        'page' => [
            'header' => [
                'title' => 'الغرف',
                'description' => 'إدارة سجلات الغرف وعرض تفاصيلها.',
            ],
        ],

        'stats' => [
            'total_rooms' => 'إجمالي الغرف',
            'total_double_rooms' => 'إجمالي الغرف المزدوجة',
            'total_beds' => 'إجمالي الأسرّة',
            'total_available_beds' => 'إجمالي الأسرّة المتاحة',
            'male_rooms' => 'غرف الذكور',
            'female_rooms' => 'غرف الإناث',
            'male_double_rooms' => 'الغرف المزدوجة للذكور',
            'female_double_rooms' => 'الغرف المزدوجة للإناث',
            'male_beds' => 'أسرّة الذكور',
            'female_beds' => 'أسرّة الإناث',
            'available_male_beds' => 'الأسرّة المتاحة للذكور',
            'available_female_beds' => 'الأسرّة المتاحة للإناث',
        ],

        'buttons' => [
            'search' => 'بحث',
            'clear_filters' => 'مسح التصفية',
            'close' => 'إغلاق',
            'save' => 'حفظ',
        ],

        'search' => [
            'title' => 'بحث متقدم',
            'labels' => [
                'building_number' => 'المبنى',
                'apartment_number' => 'الشقة',
                'gender_restriction' => 'تقييد الجنس',
                'type' => 'نوع الغرفة',
                'purpose' => 'الغرض',
            ],
            'placeholders' => [
                'all' => 'الكل',
                'select_gender' => 'اختر الجنس',
                'select_type' => 'اختر النوع',
                'select_purpose' => 'اختر الغرض',
            ],
            'options' => [
                'male' => 'ذكور',
                'female' => 'إناث',
                'mixed' => 'مختلط',
                'single' => 'فردية',
                'double' => 'مزدوجة',
                'housing' => 'سكن',
                'staff_housing' => 'سكن الموظفين',
                'office' => 'مكتب',
                'storage' => 'تخزين',
            ],
        ],

        'table' => [
            'headers' => [
                'number' => 'الرقم',
                'apartment' => 'الشقة',
                'building' => 'المبنى',
                'type' => 'النوع',
                'purpose' => 'الغرض',
                'gender' => 'الجنس',
                'available_capacity' => 'السعة المتاحة',
                'active' => 'نشط',
                'actions' => 'الإجراءات',
            ],
        ],

        'modals' => [
            'edit' => [
                'title' => 'تعديل الغرفة',
                'labels' => [
                    'type' => 'النوع',
                    'purpose' => 'الغرض',
                    'description' => 'الوصف',
                ],
            ],
            'view' => [
                'title' => 'تفاصيل الغرفة',
                'labels' => [
                    'number' => 'الرقم',
                    'apartment' => 'الشقة',
                    'building' => 'المبنى',
                    'type' => 'النوع',
                    'gender_restriction' => 'تقييد الجنس',
                    'active' => 'نشط',
                    'capacity' => 'السعة',
                    'current_occupancy' => 'الإشغال الحالي',
                    'available_capacity' => 'السعة المتاحة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'تاريخ التحديث',
                ],
            ],
        ],

        'placeholders' => [
            'select_building' => 'اختر المبنى',
            'select_apartment' => 'اختر الشقة',
            'select_building_first' => 'اختر المبنى أولاً',
            'no_apartments' => 'لا توجد شقق',
            'select_gender' => 'اختر الجنس',
            'select_type' => 'اختر النوع',
            'select_purpose' => 'اختر الغرض',
        ],

        'status' => [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
        ],

        'messages' => [
            'load_stats_error' => 'فشل في تحميل إحصائيات الغرف',
            'load_room_error' => 'فشل في تحميل بيانات الغرفة',
            'delete_error' => 'فشل في حذف الغرفة',
            'operation_failed' => 'فشلت العملية',
            'activated' => 'تم تنشيط الغرفة بنجاح',
            'deactivated' => 'تم إلغاء تنشيط الغرفة بنجاح',
            'deleted' => 'تم حذف الغرفة',
            'saved' => 'تم حفظ الغرفة بنجاح',
        ],

        'confirm' => [
            'activate' => [
                'title' => 'تنشيط الغرفة؟',
                'text' => 'هل أنت متأكد أنك تريد تنشيط هذه الغرفة؟',
                'button' => 'نعم، قم بتنشيطها!',
            ],
            'deactivate' => [
                'title' => 'إلغاء تنشيط الغرفة؟',
                'text' => 'هل أنت متأكد أنك تريد إلغاء تنشيط هذه الغرفة؟',
                'button' => 'نعم، قم بإلغاء تنشيطها!',
            ],
            'delete' => [
                'title' => 'حذف الغرفة؟',
                'text' => "لن تتمكن من التراجع عن هذا الإجراء!",
                'button' => 'نعم، احذفها!',
            ],
        ],
];
