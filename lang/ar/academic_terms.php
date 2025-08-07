<?php

return [
    'page_title' => 'الفصول الدراسية',

    'stats' => [
        'total_terms' => 'إجمالي الفصول',
        'active_terms' => 'الفصول النشطة',
        'inactive_terms' => 'الفصول غير النشطة',
        'current_term' => 'الفصل الحالي',
        'no_current_term' => 'لا يوجد فصل حالي',
    ],

    'header' => [
        'title' => 'الفصول الدراسية',
        'description' => 'إدارة الفصول الدراسية',
    ],

    'buttons' => [
        'add_term' => 'إضافة فصل',
        'add' => 'إضافة',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'clear_filters' => 'مسح التصفية',
        'close' => 'إغلاق',
    ],

    'search' => [
        'title' => 'البحث في الفصول الدراسية',
        'labels' => [
            'season' => 'الموسم',
            'academic_year' => 'السنة الدراسية',
            'status' => 'الحالة',
        ],
        'placeholders' => [
            'all_seasons' => 'جميع المواسم',
            'all_years' => 'جميع السنوات',
            'all_status' => 'جميع الحالات',
            'select_season' => 'اختر الموسم',
            'select_year' => 'اختر السنة',
            'select_status' => 'اختر الحالة',
            'select_semester' => 'اختر الفصل',
        ],
        'options' => [
            'fall' => 'الخريف',
            'spring' => 'الربيع',
            'summer' => 'الصيف',
        ],
    ],

    'table' => [
        'headers' => [
            'season' => 'الموسم',
            'year' => 'السنة',
            'code' => 'الرمز',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'reservations' => 'الحجوزات',
            'status' => 'الحالة',
            'action' => 'الإجراء',
        ],
    ],

    'form' => [
        'labels' => [
            'season' => 'الموسم',
            'year' => 'السنة',
            'semester_number' => 'رقم الفصل',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'code' => 'الرمز',
            'active' => 'نشط',
            'current' => 'حالي',
            'activated_at' => 'تم التفعيل في',
            'started_at' => 'تم البدء في',
            'ended_at' => 'تم الانتهاء في',
            'created_at' => 'تم الإنشاء في',
            'updated_at' => 'تم التحديث في',
        ],
        'placeholders' => [
            'year' => 'سنة-سنة',
        ],
    ],

    'modal' => [
        'add_term_title' => 'إضافة فصل دراسي',
        'edit_term_title' => 'تعديل فصل دراسي',
        'view_term_title' => 'عرض فصل دراسي',
        'save_button' => 'حفظ',
        'update_button' => 'تحديث',
        'yes' => 'نعم',
        'no' => 'لا',
    ],

    'messages' => [
        'success' => [
            'created' => 'تم إنشاء الفصل الدراسي بنجاح.',
            'updated' => 'تم تحديث الفصل الدراسي بنجاح.',
            'deleted' => 'تم حذف الفصل الدراسي بنجاح.',
            'activated' => 'تم تفعيل الفصل الدراسي بنجاح.',
            'deactivated' => 'تم إلغاء تفعيل الفصل الدراسي بنجاح.',
            'started' => 'تم بدء الفصل الدراسي بنجاح.',
            'ended' => 'تم إنهاء الفصل الدراسي بنجاح.',
            'reservations_activated' => ':count حجز(ات) تم تفعيلها.',
            'no_reservations_activated' => 'لم يتم تفعيل أي حجوزات لهذا الفصل.',
            'fetched' => 'تم جلب الفصول الدراسية بنجاح.',
        ],
        'error' => [
            'stats_load_failed' => 'فشل في تحميل إحصائيات الفصول.',
            'load_failed' => 'فشل في تحميل البيانات.',
            'save_failed' => 'فشل في حفظ الفصل الدراسي.',
            'delete_failed' => 'فشل في حذف الفصل الدراسي.',
            'term_missing' => 'الفصل غير موجود.',
            'start_failed' => 'فشل في بدء الفصل الدراسي.',
            'end_failed' => 'فشل في إنهاء الفصل الدراسي.',
            'activate_failed' => 'فشل في تفعيل الفصل الدراسي.',
            'deactivate_failed' => 'فشل في إلغاء تفعيل الفصل الدراسي.',
            'season_required' => 'الموسم مطلوب.',
            'year_required' => 'السنة الدراسية مطلوبة.',
            'semester_required' => 'رقم الفصل مطلوب.',
            'start_date_required' => 'تاريخ البداية مطلوب.',
            'invalid_year_format' => 'يجب أن تكون السنة بصيغة سنة-سنة.',
            'invalid_date_range' => 'يجب أن يكون تاريخ النهاية بعد تاريخ البداية.',
            'term_not_active' => 'يجب أن يكون الفصل نشطاً قبل أن يتم بدؤه.',
            'term_already_current' => 'هذا الفصل مُعيّن بالفعل كفصل حالي.',
            'old_year' => 'لا يمكن بدء فصول من السنوات الدراسية السابقة.',
            'term_cannot_start' => "لا يمكن بدء الفصل ':new_term' بينما ':current_term' نشط حالياً.",
            'duplicate_term' => 'يوجد فصل بهذه التفاصيل بالفعل.',
            'term_not_found' => 'الفصل الدراسي غير موجود.',
            'delete_failed' => 'لا يمكن حذف الفصل طالما يحتوي على حجوزات.',
            'term_not_current' => 'الفصل ليس نشطاً حالياً.',
            'term_has_reservations' => 'لا يمكن إنهاء الفصل طالما توجد حجوزات نشطة.',
        ],
        'not_set' => 'غير محدد',
    ],

    'confirm' => [
        'delete' => [
            'title' => 'حذف الفصل الدراسي',
            'text' => 'هل أنت متأكد من رغبتك في حذف هذا الفصل الدراسي؟',
            'button' => 'حذف',
        ],
        'start' => [
            'title' => 'بدء الفصل الدراسي',
            'text' => 'هل أنت متأكد من رغبتك في بدء هذا الفصل الدراسي؟',
            'button' => 'بدء',
        ],
        'end' => [
            'title' => 'إنهاء الفصل الدراسي',
            'text' => 'هل أنت متأكد من رغبتك في إنهاء هذا الفصل الدراسي؟',
            'button' => 'إنهاء',
        ],
        'activate' => [
            'title' => 'تفعيل الفصل الدراسي',
            'text' => 'هل أنت متأكد من رغبتك في تفعيل هذا الفصل الدراسي؟',
            'button' => 'تفعيل',
        ],
        'deactivate' => [
            'title' => 'إلغاء تفعيل الفصل الدراسي',
            'text' => 'هل أنت متأكد من رغبتك في إلغاء تفعيل هذا الفصل الدراسي؟',
            'button' => 'إلغاء التفعيل',
        ],
    ],

    'term' => 'الفصل',

    'season' => [
        'Fall' => 'الخريف',
        'Spring' => 'الربيع',
        'Summer' => 'الصيف',
    ],

    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط'
    ],
    'actions' => [
        'activate' => 'تفعيل',
        'deactivate' => 'إلغاء التفعيل',
        'start_term' => 'بدء الفصل',
        'end_term' => 'إنهاء الفصل'
    ],
];