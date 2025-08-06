<?php

return [
    // Page meta
    'page_title' => 'Reservation Management | Housing',
    'page_header' => 'Reservations',
    'page_description' => 'Manage reservations and their details.',
    
    // Create page specific
    'create' => [
        'page_title' => 'Add Reservation | Housing',
        'page_header' => 'Add Reservation',
        'page_description' => 'Create a new reservation.',
        'back_to_list' => 'Back to List',
        'enter_national_id' => 'Enter National ID',
        'national_id_placeholder' => 'Enter National ID...',
        'search' => 'Search',
        'user_info' => 'User Info',
        'accommodation_details' => 'Accommodation Details',
        'accommodation_type' => 'Accommodation Type',
        'select_type' => 'Select Type',
        'room' => 'Room',
        'apartment' => 'Apartment',
        'building' => 'Building',
        'select_building' => 'Select Building',
        'select_apartment' => 'Select Apartment',
        'select_room' => 'Select Room',
        'double_room_option' => 'Double Room Option',
        'take_one_bed' => 'Take one bed',
        'take_both_beds' => 'Take both beds',
        'period' => 'Period',
        'select_period' => 'Select Period',
        'academic' => 'Academic',
        'calendar' => 'Calendar',
        'academic_term' => 'Academic Term',
        'select_academic_term' => 'Select Academic Term',
        'check_in_date' => 'Check-in Date',
        'check_out_date' => 'Check-out Date',
        'status' => 'Status',
        'equipment' => 'Equipment',
        'additional_info' => 'Additional Info',
        'notes' => 'Notes',
        'notes_placeholder' => 'Enter any additional notes...',
        'save_reservation' => 'Save Reservation',
    ],

    // My Reservations page specific
    'my_reservations' => [
        'page_title' => 'My Reservations',
        'page_header' => 'My Reservations',
        'page_description' => 'View and manage your current housing reservation details below.',
        'new_reservation' => 'New Reservation',
        'property' => 'Property',
        'property_placeholder' => 'Search by property name',
    ],

    // Check-in page specific
    'check_in' => [
        'page_title' => 'Check-in | Housing',
        'page_header' => 'Guest Check-in',
        'page_description' => 'Process guest check-in with equipment assignment.',
        'back_to_list' => 'Back to List',
        'search_reservation' => 'Search Reservation',
        'reservation_number_placeholder' => 'Enter Reservation Number...',
        'search_reservation_btn' => 'Search Reservation',
        'reservation_details' => 'Reservation Details',
        'guest_information' => 'Guest Information',
        'accommodation_info' => 'Accommodation Information',
        'equipment_assignment' => 'Equipment Assignment',
        'check_in_form' => 'Check-in Form',
        'complete_check_in' => 'Complete Check-in',
        'check_in_date' => 'Check-in Date',
        'check_in_time' => 'Check-in Time',
        'notes' => 'Notes',
        'special_requests' => 'Special Requests',
        'confirm_check_in' => 'Confirm Check-in',
    ],

    // Check-out page specific
    'check_out' => [
        'page_title' => 'Check-out | Housing',
        'page_header' => 'Guest Check-out',
        'page_description' => 'Process guest check-out and equipment return.',
        'back_to_list' => 'Back to List',
        'search_reservation' => 'Search Active Reservation',
        'reservation_number_placeholder' => 'Enter Reservation Number...',
        'search_reservation_btn' => 'Search Reservation',
        'reservation_details' => 'Reservation Details',
        'guest_information' => 'Guest Information',
        'accommodation_info' => 'Accommodation Information',
        'equipment_return' => 'Equipment Return',
        'check_out_form' => 'Check-out Form',
        'complete_check_out' => 'Complete Check-out',
        'check_out_date' => 'Check-out Date',
        'check_out_time' => 'Check-out Time',
        'room_condition' => 'Room Condition',
        'damages_reported' => 'Damages Reported',
        'cleaning_status' => 'Cleaning Status',
        'final_notes' => 'Final Notes',
        'confirm_check_out' => 'Confirm Check-out',
    ],
    
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