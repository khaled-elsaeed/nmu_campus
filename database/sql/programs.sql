-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:55001
-- Generation Time: Jul 18, 2025 at 02:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

-- --------------------------------------------------------

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name_ar`, `name_en`, `duration_years`, `faculty_id`, `created_at`, `updated_at`) VALUES
(1, 'ريادة الأعمال والابتكار', 'Entrepreneurship & Innovation', 4, 1, NOW(), NOW()),
(2, 'الإدارة', 'Management', 4, 1, NOW(), NOW()),
(3, 'المالية والاستثمار', 'Finance & Investment', 4, 1, NOW(), NOW()),
(4, 'المحاسبة ونظم المعلومات', 'Accounting & Information Systems', 4, 1, NOW(), NOW()),
(5, 'التسويق', 'Marketing', 4, 1, NOW(), NOW()),
(6, 'اللوجستيات وإدارة سلاسل التوريد', 'Logistics & Supply-Chains Management', 4, 1, NOW(), NOW()),
(7, 'الاقتصاد والعلوم السياسية', 'Economics & Political Sciences', 4, 1, NOW(), NOW()),
(8, 'القانون', 'Law', 4, 2, NOW(), NOW()),
(9, 'هندسة الإعلام والتكنولوجيا', 'Media Engineering & Technology', 4, 3, NOW(), NOW()),
(10, 'هندسة وتكنولوجيا تنفيذ الأعمال المدنية', 'Engineering & Technology Implementation of Civil Works', 4, 3, NOW(), NOW()),
(11, 'هندسة تطوير المنتجات', 'Product Development Engineering', 4, 3, NOW(), NOW()),
(12, 'هندسة الطيران والفضاء', 'Aeronautical & Aerospace Engineering', 4, 3, NOW(), NOW()),
(13, 'هندسة البترول والغاز', 'Petroleum & Gas Engineering', 4, 3, NOW(), NOW()),
(14, 'العمارة البيئية وتكنولوجيا البناء', 'Environmental Architecture & Technology', 4, 3, NOW(), NOW()),
(15, 'هندسة الميكاترونيك', 'Mechatronics Engineering', 4, 3, NOW(), NOW()),
(16, 'هندسة الطاقة', 'Energy Engineering', 4, 3, NOW(), NOW()),
(17, 'الهندسة الطبية الحيوية', 'Biomedical Engineering', 4, 3, NOW(), NOW()),
(18, 'هندسة الحاسب', 'Computer Engineering', 4, 4, NOW(), NOW()),
(19, 'هندسة الذكاء الاصطناعي', 'Artificial Intelligence Engineering', 4, 4, NOW(), NOW()),
(20, 'علوم الحاسب', 'Computer Science', 4, 4, NOW(), NOW()),
(21, 'علوم الذكاء الاصطناعي', 'Artificial Intelligence Science', 4, 4, NOW(), NOW()),
(22, 'المعلوماتية الطبية الحيوية', 'Biomedical Informatics', 4, 4, NOW(), NOW()),
(23, 'الكيمياء الصناعية', 'Industrial Chemistry', 4, 5, NOW(), NOW()),
(24, 'البيولوجيا الجزيئية', 'Molecular Biology', 4, 5, NOW(), NOW()),
(25, 'علوم الأدلة الجنائية', 'Forensic Science', 4, 5, NOW(), NOW()),
(26, 'التكنولوجيا الحيوية', 'Molecular Biotechnology', 4, 5, NOW(), NOW()),
(27, 'الطب والجراحة', 'Medicine & Surgery', 6, 6, NOW(), NOW()),
(28, 'طب الأسنان', 'Dentistry', 6, 7, NOW(), NOW()),
(29, 'فارم دي كلينيكال', 'Pharm-D Clinical', 4, 8, NOW(), NOW()),
(30, 'تكنولوجيا المختبرات الطبية', 'Medical Laboratory Technology', 4, 9, NOW(), NOW()),
(31, 'تكنولوجيا الأشعة والتصوير الطبي', 'Technology of Radiology & Medical Imaging', 4, 9, NOW(), NOW()),
(32, 'تكنولوجيا تصنيع الأسنان', 'Technology of Prosthetic Dentistry', 4, 9, NOW(), NOW()),
(33, 'تكنولوجيا التخدير والعناية المركزة', 'Technology of Anesthesia & Intensive Care', 4, 9, NOW(), NOW()),
(34, 'تكنولوجيا العلاج التنفسي', 'Technology of Respiratory Therapy', 4, 9, NOW(), NOW()),
(35, 'تكنولوجيا الأطراف الصناعية والأجهزة التقويمية', 'Technology of Prosthetic & Orthotic Devices', 4, 9, NOW(), NOW()),
(36, 'العلاج الوظيفي', 'Occupational Therapy', 4, 9, NOW(), NOW()),
(37, 'تكنولوجيا البصريات', 'Technology of Optics', 4, 9, NOW(), NOW()),
(38, 'علوم التمريض', 'Nursing Science', 4, 10, NOW(), NOW()),
(39, 'علوم وهندسة مواد النسيج', 'Textile Material Science & Engineering', 4, 11, NOW(), NOW()),
(40, 'هندسة البوليمرات والألوان في النسيج', 'Textile Polymer & Color Chemistry Engineering', 4, 11, NOW(), NOW()),
(41, 'إدارة النسيج والأزياء والترويج', 'Textile & Apparel Management & Merchandising', 4, 11, NOW(), NOW()),
(42, 'تصميم النسيج والأزياء', 'Textile & Fashion Design', 4, 11, NOW(), NOW()),
(43, 'اللغات التطبيقية', 'Applied Languages', 4, 12, NOW(), NOW()),
(44, 'علم الاجتماع', 'Sociology', 4, 12, NOW(), NOW()),
(45, 'علم النفس', 'Psychology', 4, 12, NOW(), NOW()),
(46, 'الجغرافيا', 'Geography', 4, 12, NOW(), NOW()),
(47, 'التاريخ', 'History', 4, 12, NOW(), NOW()),
(48, 'العلوم السياسية', 'Political Sciences', 4, 12, NOW(), NOW()),
(49, 'المكتبات والمعلومات والأرشيف', 'Library, Information & Archive', 4, 12, NOW(), NOW()),
(50, 'إرشاد السياحة', 'Tourism Guidance', 4, 12, NOW(), NOW()),
(51, 'الاتصال العام والمتخصص', 'Public & Specialized Communication', 4, 13, NOW(), NOW()),
(52, 'تحرير الأخبار والكتابة', 'News Editing & Writing', 4, 13, NOW(), NOW()),
(53, 'إنتاج الإعلام والاتصال الرقمي', 'Media Production & Digital Communication', 4, 13, NOW(), NOW());
