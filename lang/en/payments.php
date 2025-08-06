<?php

return [
    // Payment Management
    'page_title' => 'Payment Management | Housing',
    'page_header' => 'Payments',
    'page_description' => 'Manage payment records and financial transactions.',
    
    // Statistics
    'stats' => [
        'total_payments' => 'Total Payments',
        'male_payments' => 'Male Payments',
        'female_payments' => 'Female Payments',
        'pending_payments' => 'Pending Payments',
        'completed_payments' => 'Completed Payments',
        'overdue_payments' => 'Overdue Payments',
    ],

    // Actions
    'actions' => [
        'add_payment' => 'Add Payment',
        'edit_payment' => 'Edit Payment',
        'delete_payment' => 'Delete Payment',
        'view_payment' => 'View Payment',
        'process_payment' => 'Process Payment',
        'mark_paid' => 'Mark as Paid',
        'send_reminder' => 'Send Reminder',
    ],

    // Fields
    'fields' => [
        'payment_id' => 'Payment ID',
        'student_name' => 'Student Name',
        'amount' => 'Amount',
        'payment_type' => 'Payment Type',
        'due_date' => 'Due Date',
        'payment_date' => 'Payment Date',
        'status' => 'Status',
        'method' => 'Payment Method',
        'reference' => 'Reference Number',
        'notes' => 'Notes',
    ],

    // Payment Types
    'types' => [
        'housing_fee' => 'Housing Fee',
        'deposit' => 'Security Deposit',
        'utilities' => 'Utilities',
        'maintenance' => 'Maintenance Fee',
        'late_fee' => 'Late Fee',
        'other' => 'Other',
    ],

    // Payment Status
    'status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],

    // Payment Methods
    'methods' => [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
        'check' => 'Check',
        'online' => 'Online Payment',
    ],

    // Insurance
    'insurance' => [
        'page_title' => 'Insurance Management',
        'page_header' => 'Insurance',
        'page_description' => 'Manage insurance policies and claims.',
        'add_insurance' => 'Add Insurance',
        'policy_number' => 'Policy Number',
        'provider' => 'Insurance Provider',
        'coverage_amount' => 'Coverage Amount',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'premium' => 'Premium',
    ],

    // Search and Filters
    'search' => [
        'search_placeholder' => 'Search payments...',
        'filter_by_status' => 'Filter by Status',
        'filter_by_type' => 'Filter by Type',
        'date_range' => 'Date Range',
        'clear_filters' => 'Clear Filters',
    ],

    // Messages
    'messages' => [
        'success' => [
            'payment_created' => 'Payment record has been created successfully.',
            'payment_updated' => 'Payment record has been updated successfully.',
            'payment_deleted' => 'Payment record has been deleted successfully.',
            'payment_processed' => 'Payment has been processed successfully.',
            'reminder_sent' => 'Payment reminder has been sent successfully.',
        ],
        'error' => [
            'payment_create_failed' => 'Failed to create payment record.',
            'payment_update_failed' => 'Failed to update payment record.',
            'payment_delete_failed' => 'Failed to delete payment record.',
            'payment_process_failed' => 'Failed to process payment.',
            'invalid_amount' => 'Invalid payment amount.',
            'load_failed' => 'Failed to load payment data.',
        ],
    ],

    // Common
    'common' => [
        'search' => 'Search',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'close' => 'Close',
        'actions' => 'Actions',
        'all' => 'All',
        'select_option' => 'Select Option',
    ],
];
