<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

session_start();

$prop_hin = [
    "name" => "मालिक",
    "flat" => "घर",
    "flatNo" => "<i class=\"bi bi-list-check\"></i> घर नंबर",
    "nameSearch" => "<i class=\"bi bi-list-check\"> </i>",
    "email" => "<i class=\"bi bi-envelope-at\"> ईमेल</i>",
    "save" => "<i class=\"bi bi-floppy\"> सहेजें</i>",
    "add" => "<i class=\"bi bi-floppy\"> जोड़ना</i>",
    "find" => "<i class=\"bi bi-search\"></i> खोज",
    "login" => "<i class=\"bi bi-box-arrow-in-right\"></i> लॉग इन करें",
    "register" => "<i class=\"bi bi-floppy\"></i> पंजीकरण करें",
    "noFlat" => "<b>फ़्लैट विवरण नहीं मिला!</b><br><i class=\"bi bi-question-diamond-fill\"></i> क्या यह वैध फ़्लैट नंबर है?",
    "noOwner" => "<b>मालिकों का विवरण नहीं मिला!</b><br><i class=\"bi bi-question-diamond-fill\"></i> कीवर्ड बदलें और फिर से प्रयास करें!",
    "noSociety" => "<b>सोसाइटी विवरण नहीं मिला!</b><br><i class=\"bi bi-question-diamond-fill\"></i> कुछ सोसाइटी विवरण जोड़ें!",
    "failFlat" => "<i class=\"bi bi-exclamation-triangle\"></i> फ़्लैट विवरण प्राप्त करने में असमर्थ.",
    "failOwner" => "<i class=\"bi bi-exclamation-triangle\"></i> स्वामियों का विवरण प्राप्त करने में असमर्थ.",
    "failSociety" => "<i class=\"bi bi-exclamation-triangle\"></i> सोसाइटी का विवरण प्राप्त करने में असमर्थ.",
    "blankInput" => "<i class=\"bi bi-exclamation-triangle\"></i> हम खाली डेटा सहेज नहीं सकते.",
    "myDetails" => "<i class=\"bi bi-list-check\"></i> फ्लैट विवरण",
    "contactSociety" => "कृपया इस विवरण को हटाने के लिए सोसाइटी कार्यालय से संपर्क करें.",
    "modalEmail" => "अपडेट करें <i class=\"bi bi-envelope-at\"> ईमेल आईडी",
    "failEmail" => "<i class=\"bi bi-exclamation-triangle\"></i> ईमेल सहेजने में असमर्थ.",
    "emailFirst" => "<label style='color: red;'><i class='bi bi-exclamation-triangle'></i> सबसे पहले ईमेल पता जोड़ें!</label>",
    "inputEmail" => "मान्य ईमेल पता टाइप करें",
    "inputFlat" => "फ्लैट नंबर या स्वामी का नाम टाइप करें",
    "inputCode" => "ईमेल में प्राप्त कोड टाइप करें",
    "inputPass" => "पासवर्ड टाइप करें",
    "chairman" => "अध्यक्ष",
    "secretary" => "सचिव",
    "treasurer" => "कोषाध्यक्ष",
    "impcontacts" => "महत्वपूर्ण संपर्क:",
];

$prop_eng = [
    "name" => "OWNER",
    "flat" => "FLAT",
    "flatNo" => "<i class=\"bi bi-list-check\"></i> Flat No.",
    "nameSearch" => "<i class=\"bi bi-list-check\"> </i>",
    "email" => "<i class=\"bi bi-envelope-at\"> Email</i>",
    "save" => "<i class=\"bi bi-floppy\"> Save</i>",
    "add" => "<i class=\"bi bi-floppy\"> Add</i>",
    "find" => "<i class=\"bi bi-search\"></i> Find",
    "login" => "<i class=\"bi bi-box-arrow-in-right\"></i> Login",
    "register" => "<i class=\"bi bi-floppy\"></i> Register",
    "noFlat" => "<b>FLAT DETAILS NOT FOUND!</b><br><i class=\"bi bi-question-diamond-fill\"></i> Is this valid flat number?",
    "noOwner" => "<b>OWNERS DETAILS NOT FOUND!</b><br><i class=\"bi bi-question-diamond-fill\"></i> Change keyword and try again!",
    "noSociety" => "<b>SOCIETY DETAILS NOT FOUND!</b><br><i class=\"bi bi-question-diamond-fill\"></i> Add some society details!",
    "failFlat" => "<i class=\"bi bi-exclamation-triangle\"></i> UNABLE TO GET FLAT DETAILS.",
    "failOwner" => "<i class=\"bi bi-exclamation-triangle\"></i> UNABLE TO GET OWNERS DETAILS.",
    "failSociety" => "<i class=\"bi bi-exclamation-triangle\"></i> UNABLE TO GET SOCIETY DETAILS.",
    "blankInput" => "<i class=\"bi bi-exclamation-triangle\"></i> We cannot save blank data.",
    "myDetails" => "<i class=\"bi bi-list-check\"></i> Flat Details",
    "contactSociety" => "Please contact society office for removing this detail.",
    "modalEmail" => "Update <i class=\"bi bi-envelope-at\"> Email ID",
    "failEmail" => "<i class=\"bi bi-exclamation-triangle\"></i> UNABLE TO SAVE EMAIL.",
    "emailFirst" => "<label style='color: red;'><i class='bi bi-exclamation-triangle'></i> Add email address first!</label>",
    "inputEmail" => "Type valid email address",
    "inputFlat" => "Type flat number or owner name",
    "inputCode" => "Type code received in email",
    "inputPass" => "Type Password",
    "chairman" => "Chairman",
    "secretary" => "Secretary",
    "treasurer" => "Treasurer",
    "impcontacts" => "Important Contacts:",
];


switch ($_SESSION['lang']) {
    case 'eng':
        $prop = $prop_eng;
        break;
    case 'hin':
        $prop = $prop_hin;
        break;
    default:
        $prop = $prop_eng;
        break;
}
