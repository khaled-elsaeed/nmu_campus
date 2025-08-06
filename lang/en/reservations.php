<?php

return [
    // Page meta
    'page_title' => 'Reservation Management | Housing',
    'page_header' => 'Reservations',
    'page_description' => 'Manage reservations and their details.',
    
    // Statistics cards
    'stats' => [
        'total' => 'Total Reservations',
        'active' => 'Active Reservations',
        'inactive' => 'Inactive Reservations',
    ],
    
    // Search section
    'search' => [
        'advanced_title' => 'Advanced Search',
        'button_tooltip' => 'Toggle Search',
        'clear_filters' => 'Clear Filters',
        'fields' => [
            'national_id' => 'National ID',
            'status' => 'Status',
            'active' => 'Active',
            'academic_term' => 'Academic Term',
            'building' => 'Building',
            'apartment' => 'Apartment',
            'room' => 'Room',
        ],
        'placeholders' => [
            'all_statuses' => 'All Statuses',
            'all' => 'All',
            'all_terms' => 'All Terms',
            'select_building' => 'Select Building',
            'select_apartment' => 'Select Apartment',
            'select_room' => 'Select Room',
        ],
    ],
    
    // Table headers
    'table' => [
        'headers' => [
            'reservation_number' => 'Reservation #',
            'user' => 'User',
            'accommodation' => 'Accommodation',
            'academic_term' => 'Academic Term',
            'check_in' => 'Check-in',
            'check_out' => 'Check-out',
            'status' => 'Status',
            'active' => 'Active',
            'period_type' => 'Period Type',
            'created' => 'Created',
            'actions' => 'Actions',
        ],
    ],
    
    // Status options
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'checked_in' => 'Checked In',
        'checked_out' => 'Checked Out',
        'cancelled' => 'Cancelled',
    ],
    
    // Active/Inactive
    'active_status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    
    // Action buttons
    'actions' => [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel Reservation',
        'search' => 'Search',
    ],
    
    // Success messages
    'messages' => [
        'success' => [
            'created' => 'Reservation has been created successfully.',
            'updated' => 'Reservation has been updated successfully.',
            'deleted' => 'Reservation has been deleted.',
            'cancelled' => 'Reservation has been cancelled.',
        ],
        'error' => [
            'stats_load_failed' => 'Failed to load reservation statistics.',
            'load_failed' => 'Failed to load reservation data.',
            'create_failed' => 'Failed to create reservation.',
            'update_failed' => 'Failed to update reservation.',
            'delete_failed' => 'Failed to delete reservation.',
            'cancel_failed' => 'Failed to cancel reservation.',
            'buildings_load_failed' => 'Failed to load buildings.',
            'apartments_load_failed' => 'Failed to load apartments.',
            'rooms_load_failed' => 'Failed to load rooms.',
            'academic_terms_load_failed' => 'Failed to load academic terms.',
            'operation_failed' => 'Operation failed.',
        ],
    ],
    
    // Confirmation dialogs
    'confirm' => [
        'delete' => [
            'title' => 'Delete Reservation?',
            'text' => "You won't be able to revert this!",
            'button' => 'Yes, delete it!',
        ],
        'cancel' => [
            'title' => 'Cancel Reservation?',
            'text' => 'Are you sure you want to cancel this reservation?',
            'button' => 'Yes, cancel it!',
        ],
    ],
];