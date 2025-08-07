<?php

return [
    'page' => [
        'title' => 'إدارة الكليات',
        'header' => [
            'title' => 'الكليات',
            'description' => 'إدارة جميع سجلات الكليات وإضافة كليات جديدة باستخدام الخيارات على اليمين.',
        ],
    ],

    'stats' => [
        'total_faculties' => 'إجمالي الكليات',
        'with_programs' => 'الكليات مع البرامج',
        'without_programs' => 'الكليات بدون برامج',
    ],

    'buttons' => [
        'add_faculty' => 'إضافة كلية',
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
            'faculty_name' => 'اسم الكلية',
        ],
        'placeholders' => [
            'faculty_name' => 'اسم الكلية',
        ],
    ],

    'table' => [
        'headers' => [
            'name' => 'الاسم',
            'programs' => 'البرامج',
            'students' => 'الطلاب',
            'staff' => 'الموظفين',
            'action' => 'الإجراء',
        ],
    ],

    'modal' => [
        'title' => 'إضافة/تعديل كلية',
        'add_title' => 'إضافة كلية',
        'edit_title' => 'تعديل كلية',
    ],

    'form' => [
        'labels' => [
            'name_en' => 'اسم الكلية (بالإنجليزية)',
            'name_ar' => 'اسم الكلية (بالعربية)',
        ],
    ],

    'messages' => [
        'stats_fetched_successfully' => 'تم جلب الإحصائيات بنجاح.',
        'created_successfully' => 'تم إنشاء الكلية بنجاح.',
        'updated_successfully' => 'تم تحديث الكلية بنجاح.',
        'deleted_successfully' => 'تم حذف الكلية بنجاح.',
        'details_fetched_successfully' => 'تم جلب تفاصيل الكلية بنجاح.',
        'fetched_successfully' => 'تم جلب الكليات بنجاح.',
        'save_success' => 'تم حفظ الكلية بنجاح.',
        'delete_success' => 'تم حذف الكلية.',
        'not_found' => 'الكلية غير موجودة.',
        'cannot_delete_has_students_or_staff' => 'لا يمكن حذف كلية تحتوي على طلاب أو موظفين.',
        'cannot_delete_programs_have_students' => 'لا يمكن حذف كلية تحتوي على برامج بها طلاب.',
        'internal_server_error' => 'خطأ داخلي في الخادم.',
        'stats_error' => 'فشل في تحميل الإحصائيات',
        'fetch_error' => 'فشل في جلب بيانات الكلية.',
        'delete_error' => 'فشل في حذف الكلية.',
        'input_error' => 'حدث خطأ. يرجى التحقق من المدخلات.',
    ],

    'confirm' => [
        'title' => 'هل أنت متأكد؟',
        'text' => 'لن تتمكن من التراجع عن هذا!',
        'confirm_button' => 'نعم، احذف!',
    ],
];
