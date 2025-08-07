<?php

return [
    'page' => [
        'title' => 'إدارة الشقق | AcadOps',
        'header' => [
            'title' => 'الشقق',
            'description' => 'إدارة جميع سجلات الشقق وعرض تفاصيل الشقق.',
        ],
    ],

    'stats' => [
        'total_apartments' => 'إجمالي الشقق',
        'male_apartments' => 'شقق الذكور',
        'female_apartments' => 'شقق الإناث',
    ],

    'buttons' => [
        'search' => 'بحث',
        'clear_filters' => 'مسح الفلاتر',
        'close' => 'إغلاق',
    ],

    'search' => [
        'title' => 'البحث المتقدم',
        'labels' => [
            'building_number' => 'رقم المبنى',
            'apartment_number' => 'رقم الشقة',
            'gender_restriction' => 'تقييد الجنس',
            'active_status' => 'حالة النشاط',
        ],
        'placeholders' => [
            'all' => 'الكل',
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
            'building' => 'المبنى',
            'total_rooms' => 'إجمالي الغرف',
            'gender' => 'الجنس',
            'active' => 'نشط',
            'created_at' => 'تاريخ الإنشاء',
            'actions' => 'الإجراءات',
        ],
        'type' => 'الشقة',
    ],

    'modal' => [
        'view_title' => 'تفاصيل الشقة',
        'labels' => [
            'number' => 'الرقم',
            'building' => 'المبنى',
            'total_rooms' => 'إجمالي الغرف',
            'gender_restriction' => 'تقييد الجنس',
            'active' => 'نشط',
            'created_at' => 'تاريخ الإنشاء',
        ],
    ],

    'placeholders' => [
        'select_building' => 'اختر المبنى',
        'select_apartment' => 'اختر الشقة',
        'select_building_first' => 'اختر المبنى أولاً',
        'no_apartments' => 'لا توجد شقق',
    ],

    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
    ],

    'messages' => [
        'stats_fetched_successfully' => 'تم جلب الإحصائيات بنجاح.',
        'details_fetched_successfully' => 'تم جلب تفاصيل الشقة بنجاح.',
        'fetched_successfully' => 'تم جلب الشقق بنجاح.',
        'activated_successfully' => 'تم تفعيل الشقة بنجاح.',
        'deactivated_successfully' => 'تم إلغاء تفعيل الشقة بنجاح.',
        'deleted_successfully' => 'تم حذف الشقة بنجاح.',
        'not_found' => 'الشقة غير موجودة.',
        'cannot_delete_has_residents' => 'لا يمكن حذف شقة تحتوي على مقيمين.',
        'internal_server_error' => 'خطأ داخلي في الخادم.',
        'load_stats_error' => 'فشل في تحميل إحصائيات الشقق',
        'load_apartment_error' => 'فشل في تحميل بيانات الشقة',
        'delete_error' => 'فشل في حذف الشقة.',
        'operation_failed' => 'فشلت العملية',
        'activated' => 'تم تفعيل الشقة بنجاح',
        'deactivated' => 'تم إلغاء تفعيل الشقة بنجاح',
        'deleted' => 'تم حذف الشقة.',
    ],

    'confirm' => [
        'activate' => [
            'title' => 'تفعيل الشقة؟',
            'text' => 'هل أنت متأكد من تفعيل هذه الشقة؟',
            'button' => 'نعم، فعلها!',
        ],
        'deactivate' => [
            'title' => 'إلغاء تفعيل الشقة؟',
            'text' => 'هل أنت متأكد من إلغاء تفعيل هذه الشقة؟',
            'button' => 'نعم، ألغ تفعيلها!',
        ],
        'delete' => [
            'title' => 'هل أنت متأكد؟',
            'text' => 'لن تتمكن من التراجع عن هذا!',
            'button' => 'نعم، احذف!',
        ],
    ],
];
