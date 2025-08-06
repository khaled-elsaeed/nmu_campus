<?php

return [
    // Payment Management
    'page_title' => 'إدارة المدفوعات | السكن',
    'page_header' => 'المدفوعات',
    'page_description' => 'إدارة سجلات المدفوعات والمعاملات المالية.',
    
    // Statistics
    'stats' => [
        'total_payments' => 'إجمالي المدفوعات',
        'male_payments' => 'مدفوعات الذكور',
        'female_payments' => 'مدفوعات الإناث',
        'pending_payments' => 'المدفوعات المعلقة',
        'completed_payments' => 'المدفوعات المكتملة',
        'overdue_payments' => 'المدفوعات المتأخرة',
    ],

    // Actions
    'actions' => [
        'add_payment' => 'إضافة دفعة',
        'edit_payment' => 'تعديل دفعة',
        'delete_payment' => 'حذف دفعة',
        'view_payment' => 'عرض دفعة',
        'process_payment' => 'معالجة الدفعة',
        'mark_paid' => 'تحديد كمدفوع',
        'send_reminder' => 'إرسال تذكير',
    ],

    // Fields
    'fields' => [
        'payment_id' => 'رقم الدفعة',
        'student_name' => 'اسم الطالب',
        'amount' => 'المبلغ',
        'payment_type' => 'نوع الدفعة',
        'due_date' => 'تاريخ الاستحقاق',
        'payment_date' => 'تاريخ الدفع',
        'status' => 'الحالة',
        'method' => 'طريقة الدفع',
        'reference' => 'الرقم المرجعي',
        'notes' => 'ملاحظات',
    ],

    // Payment Types
    'types' => [
        'housing_fee' => 'رسوم السكن',
        'deposit' => 'مبلغ التأمين',
        'utilities' => 'المرافق',
        'maintenance' => 'رسوم الصيانة',
        'late_fee' => 'غرامة التأخير',
        'other' => 'أخرى',
    ],

    // Payment Status
    'status' => [
        'pending' => 'معلق',
        'paid' => 'مدفوع',
        'overdue' => 'متأخر',
        'cancelled' => 'ملغى',
        'refunded' => 'مسترد',
    ],

    // Payment Methods
    'methods' => [
        'cash' => 'نقداً',
        'bank_transfer' => 'تحويل بنكي',
        'credit_card' => 'بطاقة ائتمان',
        'debit_card' => 'بطاقة خصم',
        'check' => 'شيك',
        'online' => 'دفع إلكتروني',
    ],

    // Insurance
    'insurance' => [
        'page_title' => 'إدارة التأمين',
        'page_header' => 'التأمين',
        'page_description' => 'إدارة بوالص التأمين والمطالبات.',
        'add_insurance' => 'إضافة تأمين',
        'policy_number' => 'رقم الوثيقة',
        'provider' => 'شركة التأمين',
        'coverage_amount' => 'مبلغ التغطية',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ الانتهاء',
        'premium' => 'القسط',
    ],

    // Search and Filters
    'search' => [
        'search_placeholder' => 'البحث في المدفوعات...',
        'filter_by_status' => 'فلترة حسب الحالة',
        'filter_by_type' => 'فلترة حسب النوع',
        'date_range' => 'نطاق التاريخ',
        'clear_filters' => 'مسح المرشحات',
    ],

    // Messages
    'messages' => [
        'success' => [
            'payment_created' => 'تم إنشاء سجل الدفعة بنجاح.',
            'payment_updated' => 'تم تحديث سجل الدفعة بنجاح.',
            'payment_deleted' => 'تم حذف سجل الدفعة بنجاح.',
            'payment_processed' => 'تم معالجة الدفعة بنجاح.',
            'reminder_sent' => 'تم إرسال تذكير الدفع بنجاح.',
        ],
        'error' => [
            'payment_create_failed' => 'فشل في إنشاء سجل الدفعة.',
            'payment_update_failed' => 'فشل في تحديث سجل الدفعة.',
            'payment_delete_failed' => 'فشل في حذف سجل الدفعة.',
            'payment_process_failed' => 'فشل في معالجة الدفعة.',
            'invalid_amount' => 'مبلغ الدفعة غير صالح.',
            'load_failed' => 'فشل في تحميل بيانات الدفع.',
        ],
    ],
];
