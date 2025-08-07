<?php

return [
    'page' => [
        'title' => 'إدارة المباني',
        'header' => [
            'title' => 'المباني',
            'description' => 'إدارة جميع سجلات المباني وإضافة مباني جديدة.',
        ],
    ],

    'stats' => [
        'total_buildings' => 'إجمالي المباني',
        'male_buildings' => 'مباني الذكور',
        'female_buildings' => 'مباني الإناث',
    ],

    'buttons' => [
        'add_building' => 'إضافة مبنى',
        'search' => 'بحث',
        'clear_filters' => 'مسح الفلاتر',
        'close' => 'إغلاق',
        'save' => 'حفظ',
    ],

    'search' => [
        'title' => 'البحث المتقدم',
        'labels' => [
            'gender_restriction' => 'تقييد الجنس',
            'active_status' => 'حالة النشاط',
        ],
        'placeholders' => [
            'all' => 'الكل',
            'select_gender' => 'اختر الجنس',
            'select_status' => 'اختر الحالة',
        ],
        'options' => [
            'male' => 'ذكور',
            'female' => 'إناث',
            'mixed' => 'مختلط',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
        ],
    ],

    'table' => [
        'headers' => [
            'number' => 'الرقم',
            'total_apartments' => 'إجمالي الشقق',
            'total_rooms' => 'إجمالي الغرف',
            'has_double_room' => 'يحتوي على غرفة مزدوجة',
            'gender' => 'الجنس',
            'active' => 'نشط',
            'current_occupancy' => 'الإشغال الحالي',
            'actions' => 'الإجراءات',
        ],
        'type' => 'المبنى',
    ],

    'modal' => [
        'title' => 'إضافة/تعديل مبنى',
        'add_title' => 'إضافة مبنى',
        'edit_title' => 'تعديل مبنى',
        'view_title' => 'تفاصيل المبنى',
        'labels' => [
            'number' => 'الرقم',
            'total_apartments' => 'إجمالي الشقق',
            'total_rooms' => 'إجمالي الغرف',
            'gender_restriction' => 'تقييد الجنس',
            'active' => 'نشط',
            'created_at' => 'تاريخ الإنشاء',
        ],
    ],

    'form' => [
        'labels' => [
            'building_number' => 'رقم المبنى',
            'total_apartments' => 'إجمالي الشقق',
            'rooms_per_apartment' => 'الغرف لكل شقة',
            'has_double_rooms' => 'هذا المبنى يحتوي على غرف مزدوجة',
            'gender_restriction' => 'تقييد الجنس',
        ],
        'placeholders' => [
            'select_gender_restriction' => 'اختر تقييد الجنس',
        ],
        'options' => [
            'male' => 'ذكور',
            'female' => 'إناث',
            'mixed' => 'مختلط',
        ],
    ],

    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'activating' => 'جاري التفعيل...',
        'deactivating' => 'جاري إلغاء التفعيل...',
    ],

    'apartment' => [
        'title' => 'شقة',
        'double_rooms' => 'الغرف المزدوجة',
        'room' => 'غرفة',
    ],

    'messages' => [
        'stats_fetched_successfully' => 'تم جلب الإحصائيات بنجاح.',
        'created_successfully' => 'تم إنشاء المبنى بنجاح.',
        'updated_successfully' => 'تم تحديث المبنى بنجاح.',
        'deleted_successfully' => 'تم حذف المبنى بنجاح.',
        'activated_successfully' => 'تم تفعيل المبنى بنجاح.',
        'deactivated_successfully' => 'تم إلغاء تفعيل المبنى بنجاح.',
        'details_fetched_successfully' => 'تم جلب تفاصيل المبنى بنجاح.',
        'fetched_successfully' => 'تم جلب المباني بنجاح.',
        'not_found' => 'المبنى غير موجود.',
        'cannot_delete_has_apartments' => 'لا يمكن حذف مبنى يحتوي على شقق.',
        'cannot_delete_has_residents' => 'لا يمكن حذف مبنى يحتوي على مقيمين.',
        'internal_server_error' => 'خطأ داخلي في الخادم.',
        'load_stats_error' => 'فشل في تحميل إحصائيات المباني',
        'load_building_error' => 'فشل في تحميل بيانات المبنى',
        'save_error' => 'حدث خطأ. يرجى التحقق من المدخلات.',
        'delete_error' => 'فشل في حذف المبنى.',
        'activate_error' => 'فشل في تفعيل المبنى.',
        'deactivate_error' => 'فشل في إلغاء تفعيل المبنى.',
        'saved' => 'تم حفظ المبنى بنجاح.',
        'deleted' => 'تم حذف المبنى.',
        'activated' => 'تم تفعيل المبنى بنجاح.',
        'deactivated' => 'تم إلغاء تفعيل المبنى بنجاح.',
    ],

    'confirm' => [
        'activate' => [
            'title' => 'تفعيل المبنى؟',
            'text' => 'هل أنت متأكد من تفعيل هذا المبنى؟ سيكون متاحاً للاستخدام.',
            'button' => 'نعم، فعله!',
        ],
        'deactivate' => [
            'title' => 'إلغاء تفعيل المبنى؟',
            'text' => 'هل أنت متأكد من إلغاء تفعيل هذا المبنى؟ لن يكون متاحاً للحجوزات الجديدة.',
            'button' => 'نعم، ألغ تفعيله!',
        ],
        'delete' => [
            'title' => 'هل أنت متأكد؟',
            'text' => 'لن تتمكن من التراجع عن هذا!',
            'button' => 'نعم، احذف!',
        ],
    ],
];
