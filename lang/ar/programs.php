<?php

return [
    'page' => [
        'title' => 'إدارة البرامج',
        'header' => [
            'title' => 'البرامج',
            'description' => 'إدارة جميع سجلات البرامج وإضافة برامج جديدة باستخدام الخيارات على اليمين.',
        ],
    ],

    'stats' => [
        'total_programs' => 'إجمالي البرامج',
        'with_students' => 'البرامج مع الطلاب',
        'without_students' => 'البرامج بدون طلاب',
    ],

    'buttons' => [
        'add_program' => 'إضافة برنامج',
        'search' => 'بحث',
        'clear_filters' => 'مسح الفلاتر',
        'close' => 'إغلاق',
        'save' => 'حفظ',
        'update' => 'تحديث',
        'saving' => 'جاري الحفظ...',
        'updating' => 'جاري التحديث...',
    ],

    'search' => [
        'title' => 'البحث المتقدم',
        'labels' => [
            'program_name' => 'اسم البرنامج',
            'faculty' => 'الكلية',
        ],
        'placeholders' => [
            'program_name' => 'اسم البرنامج',
            'select_faculty' => 'اختر الكلية',
        ],
    ],

    'table' => [
        'headers' => [
            'name' => 'الاسم',
            'faculty' => 'الكلية',
            'students_count' => 'عدد الطلاب',
            'action' => 'الإجراء',
        ],
        'type' => 'البرنامج',
    ],

    'modal' => [
        'title' => 'إضافة/تعديل برنامج',
        'add_title' => 'إضافة برنامج',
        'edit_title' => 'تعديل برنامج',
    ],

    'form' => [
        'labels' => [
            'name_en' => 'اسم البرنامج (بالإنجليزية)',
            'name_ar' => 'اسم البرنامج (بالعربية)',
            'duration_years' => 'المدة (بالسنوات)',
            'faculty' => 'الكلية',
        ],
        'placeholders' => [
            'select_duration' => 'اختر المدة',
            'select_faculty' => 'اختر الكلية',
        ],
    ],

    'dropdown' => [
        'select_faculty' => 'اختر الكلية',
    ],

    'messages' => [
        'stats_fetched_successfully' => 'تم جلب الإحصائيات بنجاح.',
        'created_successfully' => 'تم إنشاء البرنامج بنجاح.',
        'updated_successfully' => 'تم تحديث البرنامج بنجاح.',
        'deleted_successfully' => 'تم حذف البرنامج بنجاح.',
        'details_fetched_successfully' => 'تم جلب تفاصيل البرنامج بنجاح.',
        'fetched_successfully' => 'تم جلب البرامج بنجاح.',
        'save_success' => 'تم حفظ البرنامج بنجاح.',
        'delete_success' => 'تم حذف البرنامج.',
        'not_found' => 'البرنامج غير موجود.',
        'cannot_delete_has_students' => 'لا يمكن حذف برنامج يحتوي على طلاب.',
        'internal_server_error' => 'خطأ داخلي في الخادم.',
        'stats_error' => 'فشل في تحميل إحصائيات البرامج',
        'fetch_error' => 'فشل في جلب بيانات البرنامج.',
        'delete_error' => 'فشل في حذف البرنامج.',
        'input_error' => 'حدث خطأ. يرجى التحقق من المدخلات.',
        'dropdown_error' => 'حدث خطأ.',
    ],

    'confirm' => [
        'title' => 'هل أنت متأكد؟',
        'text' => 'لن تتمكن من التراجع عن هذا!',
        'confirm_button' => 'نعم، احذف!',
    ],
];
