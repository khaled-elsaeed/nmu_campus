<?php

return [
    // Page meta
    'page_title' => 'إدارة الحجوزات | حرم جامعة NMU',
    'page_header' => 'الحجوزات',
    'page_description' => 'إدارة الحجوزات وتفاصيلها.',
    
    // Statistics cards
    'stats' => [
        'total' => 'إجمالي الحجوزات',
        'active' => 'الحجوزات النشطة',
        'inactive' => 'الحجوزات غير النشطة',
    ],
    
    // Search section
    'search' => [
        'advanced_title' => 'البحث المتقدم',
        'button_tooltip' => 'تبديل البحث',
        'clear_filters' => 'مسح المرشحات',
        'fields' => [
            'national_id' => 'رقم الهوية',
            'status' => 'الحالة',
            'active' => 'نشط',
            'academic_term' => 'الفصل الأكاديمي',
            'building' => 'المبنى',
            'apartment' => 'الشقة',
            'room' => 'الغرفة',
        ],
        'placeholders' => [
            'all_statuses' => 'جميع الحالات',
            'all' => 'الكل',
            'all_terms' => 'جميع الفصول',
            'select_building' => 'اختر المبنى',
            'select_apartment' => 'اختر الشقة',
            'select_room' => 'اختر الغرفة',
        ],
    ],
    
    // Table headers
    'table' => [
        'headers' => [
            'reservation_number' => 'رقم الحجز',
            'user' => 'المستخدم',
            'accommodation' => 'السكن',
            'academic_term' => 'الفصل الأكاديمي',
            'check_in' => 'تسجيل الدخول',
            'check_out' => 'تسجيل الخروج',
            'status' => 'الحالة',
            'active' => 'نشط',
            'period_type' => 'نوع الفترة',
            'created' => 'تاريخ الإنشاء',
            'actions' => 'الإجراءات',
        ],
    ],
    
    // Status options
    'status' => [
        'pending' => 'في الانتظار',
        'confirmed' => 'مؤكد',
        'checked_in' => 'تم تسجيل الدخول',
        'checked_out' => 'تم تسجيل الخروج',
        'cancelled' => 'ملغي',
    ],
    
    // Active/Inactive
    'active_status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
    ],
    
    // Action buttons
    'actions' => [
        'view' => 'عرض',
        'edit' => 'تحرير',
        'delete' => 'حذف',
        'cancel' => 'إلغاء الحجز',
        'search' => 'بحث',
    ],
    
    // Success messages
    'messages' => [
        'success' => [
            'created' => 'تم إنشاء الحجز بنجاح.',
            'updated' => 'تم تحديث الحجز بنجاح.',
            'deleted' => 'تم حذف الحجز.',
            'cancelled' => 'تم إلغاء الحجز.',
        ],
        'error' => [
            'stats_load_failed' => 'فشل في تحميل إحصائيات الحجوزات.',
            'load_failed' => 'فشل في تحميل بيانات الحجز.',
            'create_failed' => 'فشل في إنشاء الحجز.',
            'update_failed' => 'فشل في تحديث الحجز.',
            'delete_failed' => 'فشل في حذف الحجز.',
            'cancel_failed' => 'فشل في إلغاء الحجز.',
            'buildings_load_failed' => 'فشل في تحميل المباني.',
            'apartments_load_failed' => 'فشل في تحميل الشقق.',
            'rooms_load_failed' => 'فشل في تحميل الغرف.',
            'academic_terms_load_failed' => 'فشل في تحميل الفصول الأكاديمية.',
            'operation_failed' => 'فشلت العملية.',
        ],
    ],
    
    // Confirmation dialogs
    'confirm' => [
        'delete' => [
            'title' => 'حذف الحجز؟',
            'text' => 'لن تتمكن من التراجع عن هذا الإجراء!',
            'button' => 'نعم، احذفه!',
        ],
        'cancel' => [
            'title' => 'إلغاء الحجز؟',
            'text' => 'هل أنت متأكد من أنك تريد إلغاء هذا الحجز؟',
            'button' => 'نعم، ألغه!',
        ],
    ],
];