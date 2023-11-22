<?php
ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include_once 'config.php';

$course = $_GET['course'];
$as = $_GET['as'];
$imageType = $_GET['imageType'];
$acad_sel = $_GET['acad_sel'];
$field_sel = $_GET['field_sel'];
//batch NAme
$batch_Name = mysqli_query($mysqli, "SELECT * FROM `setup_batchmaster` where Id = '$course'");
$r_batch_name = mysqli_fetch_array($batch_Name);

$acad_details = mysqli_query($mysqli, "SELECT * FROM `setup_academicyear` where Id = '$acad_sel'");
$r_acad_details = mysqli_fetch_array($acad_details);

if ($course == 'all') {
    $r_batch_name['Batch_Name'] = 'All';
}

if ($field_sel == '0') {
    $sel = "1,2,3,4,5,6,7";
} else if ($field_sel == '1') {
    $sel = "1,2";
} else if ($field_sel == '2') {
    $sel = "3,4,5";
} else if ($field_sel == '3') {
    $sel = "6,7";
}


$sql = "SELECT DISTINCT(user_applicationdetails.Application_No),user_studentdetails.Student_name,user_studentbatchmaster.roll_no,user_candidatedetails.photograph,user_studentdetails.studentregister_Id,user_applicationdetails.Id As AD_Id,user_studentregister.student_Id,user_candidatedetails.candidateRegister_Id As CR_Id FROM user_studentbatchmaster INNER JOIN user_applicationdetails ON user_studentbatchmaster.applicationDetails_Id = user_applicationdetails.Id INNER JOIN user_studentregister ON user_studentbatchmaster.studentRegister_Id = user_studentregister.Id INNER JOIN fee_receipts ON user_studentbatchmaster.applicationDetails_Id = fee_receipts.applicationdetails_id INNER JOIN setup_batchmaster ON user_studentbatchmaster.batchMaster_Id = setup_batchmaster.Id INNER JOIN setup_programmaster ON setup_batchmaster.programMaster_Id = setup_programmaster.Id INNER JOIN user_studentdetails ON user_studentdetails.studentregister_Id = user_studentregister.Id JOIN user_candidatedetails ON user_candidatedetails.candidateRegister_Id = user_studentregister.candidateRegister_Id WHERE setup_batchmaster.academicYear_Id = '$acad_sel' AND user_studentbatchmaster.Admission_Status IN (1,3) AND setup_programmaster.yearOfProgram IN ($sel) ";

if ($course != 'all') {
    $sql .= " AND setup_batchmaster.Id = '$course'";
}

$sql .= " Order By user_applicationdetails.Id";

$photo_details = mysqli_query($mysqli, $sql);

// $tmpFile = tempnam('/tmp', '');


//Personal Document Path
if ($imageType == 'Photo') {
    $image_path = './Documents/2020-21/Photo/';
} elseif ($imageType == 'Sign') {
    $image_path = './Documents/2020-21/Sign/';
} elseif ($imageType == 'Aadhaar') {
    $image_path = './Documents/2020-21/Aadhaar/';
} elseif ($imageType == 'MotherPhoto') {
    $image_path = './Documents/2020-21/MotherPhoto/';
} elseif ($imageType == 'FatherPhoto') {
    $image_path = './Documents/2020-21/FatherPhoto/';
} elseif ($imageType == 'RationCard') {
    $image_path = './Documents/2020-21/RationCard/';
} elseif ($imageType == 'Caste') {
    $image_path = './Documents/2020-21/Caste/';
} elseif ($imageType == 'PH') {
    $image_path = './Documents/2020-21/PH/';
}

//Acadamic Document Path
elseif ($imageType == 'XLeaving') {
    $image_path = './Documents/2020-21/XLeaving/';
} elseif ($imageType == 'XIILeaving') {
    $image_path = './Documents/2020-21/XIILeaving/';
} elseif ($imageType == 'XPassingCertificate') {
    $image_path = './Documents/2020-21/XPassingCertificate/';
} elseif ($imageType == 'XMarksheet') {
    $image_path = './Documents/2020-21/XMarksheet/';
} elseif ($imageType == 'XIMarksheet') {
    $image_path = './Documents/2020-21/XIMarksheet/';
} elseif ($imageType == 'XIIMarksheet') {
    $image_path = './Documents/2020-21/XIIMarksheet/';
} elseif ($imageType == 'FYSEM1Marksheet') {
    $image_path = './Documents/2020-21/FYSEM1Marksheet/';
} elseif ($imageType == 'FYSEM2Marksheet') {
    $image_path = './Documents/2020-21/FYSEM2Marksheet/';
} elseif ($imageType == 'SYSEM1Marksheet') {
    $image_path = './Documents/2020-21/SYSEM1Marksheet/';
} elseif ($imageType == 'SYSEM2Marksheet') {
    $image_path = './Documents/2020-21/SYSEM2Marksheet/';
} elseif ($imageType == 'TYSEM1Marksheet') {
    $image_path = './Documents/2020-21/TYSEM1Marksheet/';
}


//Other Document Path
elseif ($imageType == 'AdditionalDoc1') {
    $image_path = './Documents/2020-21/AdditionalDoc1/';
} elseif ($imageType == 'AdditionalDoc2') {
    $image_path = './Documents/2020-21/AdditionalDoc2/';
} elseif ($imageType == 'StudentAntiRagging') {
    $image_path = './Documents/2020-21/StudentAntiRagging/';
} elseif ($imageType == 'ParentAntiRagging') {
    $image_path = './Documents/2020-21/ParentAntiRagging/';
} elseif ($imageType == 'FreeshipDeclaration') {
    $image_path = './Documents/2020-21/FreeshipDeclaration/';
} elseif ($imageType == 'MinorityCertificate') {
    $image_path = './Documents/2020-21/MinorityCertificate/';
} elseif ($imageType == 'ManagementLetter') {
    $image_path = './Documents/2020-21/ManagementLetter/';
}

if ($imageType == 'ApplicationForm') {
    $image_path = './Documents/2020-21/ApplicationForm/';
}

if ($acad_sel == '3') {
    if ($imageType == 'Photo') {
        $image_path = '../candidateform/2019-2020/id_photos/';
    }
}

//create Temp Folder
$tempFolder = "./Download/SaveFolder_" . $SectionMaster_Id . "_" . $imageType . "_" . date("Y-m-d-h-i-s");
mkdir($tempFolder, 777);

$count = 0;
$temfiles = [];
$setCount = 0;
while ($rows = mysqli_fetch_array($photo_details)) {
    $file = '';
    if ($acad_sel == '3') {
        if ($imageType == 'Photo') {
            $file_path1 = $image_path . "reg-" . $rows['CR_Id'] . "-photo.jpg";
            $file_path2 = $image_path . "reg-" . $rows['CR_Id'] . "-photo.png";
            $file_path3 = $image_path . "reg-" . $rows['CR_Id'] . "-photo.jpeg";
            $file_path4 = $image_path . "reg-" . $rows['CR_Id'] . "-photo.pdf";

            $file_path5 = $image_path . "reg-" . $rows['CR_Id'] . "-photo.*";
            $file_path6 = $image_path . $rows['CR_Id'] . ".*";
            $file_path7 = '../candidateform/2018-19/id_photos/' . "reg-" . $rows['CR_Id'] . "-photo.*";
            $file_path8 = '../candidateform/2018-19/id_photos/' . $rows['CR_Id'] . ".*";

            $result = glob($file_path5);
            $result1 = glob($file_path6);
            $result2 = glob($file_path7);
            $result3 = glob($file_path8);

            if (!empty($result)) {
                $file = $result[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result1)) {
                $file = $result1[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result2)) {
                $file = $result2[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result3)) {
                $file = $result3[0];
                $ext['extension'] = pathinfo($file);
            }
        }

        if ($imageType == 'Sign') {
            $file_path1 = $image_path . "reg-" . $rows['CR_Id'] . "-sign.jpg";
            $file_path2 = $image_path . "reg-" . $rows['CR_Id'] . "-sign.png";
            $file_path3 = $image_path . "reg-" . $rows['CR_Id'] . "-sign.jpeg";
            $file_path4 = $image_path . "reg-" . $rows['CR_Id'] . "-sign.pdf";

            $file_path5 = $image_path . "reg-" . $rows['CR_Id'] . "-sign.*";
            $file_path6 = $image_path . $rows['CR_Id'] . ".*";
            $file_path7 = '../candidateform/2018-19/id_sign/' . "reg-" . $rows['CR_Id'] . "-sign.*";
            $file_path8 = '../candidateform/2018-19/id_sign/' . $rows['CR_Id'] . ".*";

            $result = glob($file_path5);
            $result1 = glob($file_path6);
            $result2 = glob($file_path7);
            $result3 = glob($file_path8);

            if (!empty($result)) {
                $file = $result[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result1)) {
                $file = $result1[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result2)) {
                $file = $result2[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result3)) {
                $file = $result3[0];
                $ext['extension'] = pathinfo($file);
            }
        }
    } else if ($acad_sel == '2') {

        if ($imageType == 'Photo') {
            $file_path1 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-photo.jpg";
            $file_path2 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-photo.png";
            $file_path3 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-photo.jpeg";
            $file_path4 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-photo.pdf";

            $file_path5 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-photo.*";
            $file_path6 = $image_path . $r_id_details['CR_Id'] . ".*";

            $result = glob($file_path5);
            $result1 = glob($file_path6);

            if (!empty($result)) {
                $file = $result[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result1)) {
                $file = $result1[0];
                $ext['extension'] = pathinfo($file);
            }
        }

        if ($imageType == 'Sign') {
            $file_path1 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-sign.jpg";
            $file_path2 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-sign.png";
            $file_path3 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-sign.jpeg";
            $file_path4 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-sign.pdf";

            $file_path5 = $image_path . "reg-" . $r_id_details['CR_Id'] . "-sign.*";
            $file_path6 = $image_path . $r_id_details['CR_Id'] . ".*";

            $result = glob($file_path5);
            $result1 = glob($file_path6);

            if (!empty($result)) {
                $file = $result[0];
                $ext['extension'] = pathinfo($file);
            } else if (!empty($result1)) {
                $file = $result1[0];
                $ext['extension'] = pathinfo($file);
            }
        }
    } else {

        $file_path1 = $image_path . "" . $rows['CR_Id'] . ".jpg";
        $file_path2 = $image_path . "" . $rows['CR_Id'] . ".png";
        $file_path3 = $image_path . "" . $rows['CR_Id'] . ".jpeg";
        $file_path4 = $image_path . "" . $rows['CR_Id'] . ".pdf";

        if (file_exists($file_path1)) {
            $file = $file_path1;
            $ext['extension'] = 'jpg';
        } elseif (file_exists($file_path2)) {
            $file = $file_path2;
            $ext['extension'] = 'png';
        } elseif (file_exists($file_path3)) {
            $file = $file_path3;
            $ext['extension'] = 'jpeg';
        } elseif (file_exists($file_path4)) {
            $file = $file_path4;
            $ext['extension'] = 'pdf';
        }
    }

    if ($as == 'Name') {
        $file_name = $rows['Surname'] . " " . $rows['Student_name'] . " " . $rows['Father_name'] . " " . $rows['Mother_name'] . "." . $ext['extension'];
    } elseif ($as == 'Application_No') {
        $file_name = $rows['Application_No'] . "." . $ext['extension'];
    } elseif ($as == 'GR_NO') {
        if ($rows['GR_No'] == null or $rows['GR_No'] == '') {
            $file_name = $rows['Student_name'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['GR_No'] . "." . $ext['extension'];
        }
    } elseif ($as == 'RollNo') {
        if ($rows['roll_no'] == null or $rows['roll_no'] == '') {
            $file_name = $rows['Student_name'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['roll_no'] . "." . $ext['extension'];
        }
    } elseif ($as == 'Student_Id') {
        if ($rows['student_Id'] == null or $rows['student_Id'] == '') {
            $file_name = $rows['Student_name'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['student_Id'] . "." . $ext['extension'];
        }
    } elseif ($as == 'Registeration_Id') {
        if ($rows['studentregister_Id'] == null or $rows['studentregister_Id'] == '') {
            $file_name = $rows['Student_name'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['studentregister_Id'] . "." . $ext['extension'];
        }
    } elseif ($as == 'PRN_NO') {
        if ($rows['PRN'] == null or $rows['PRN'] == '') {
            $file_name = $rows['Student_name'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['PRN'] . "." . $ext['extension'];
        }
    } elseif ($as == 'CR_Id') {
        if ($rows['CR_Id'] == null or $rows['CR_Id'] == '') {
            $file_name = $rows['CR_Id'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['CR_Id'] . "." . $ext['extension'];
        }
    } elseif ($as == 'ApplicationDetail_Id') {
        if ($rows['AD_Id'] == null or $rows['AD_Id'] == '') {
            $file_name = $rows['Student_name'] . "." . $ext['extension'];
        } else {
            $file_name = $rows['AD_Id'] . "." . $ext['extension'];
        }
    } elseif ($as == 'OriginalFileName') {

        $file_name = $rows['CR_Id'] . "." . $ext['extension'];
    } else {
        $file_name = $rows['Surname'] . " " . $rows['Student_name'] . " " . $rows['Father_name'] . " " . $rows['Mother_name'] . "." . $ext['extension'];
    }

    if (file_exists($file) && !empty($file)) {
        $saved_file = $tempFolder . "/" . $file_name;
        $upload_to_folder = file_put_contents($saved_file, file_get_contents($file));
        //save the file by using base name 
        // echo $upload_to_folder;
        if($upload_to_folder){
            echo  'File downloaded successfully!';
            $count++;
        }
    }


    if ($count == 500) {

        $download_filename = $r_acad_details['Academic_year'] . '-' . $r_batch_name['Batch_Name'] . '-' . $as . '-' . $imageType . '-' . date("Y-m-d_H-i-s") . '-' . $setCount . ".zip";
        $fileLocation = './Download/' . $download_filename;
        $temfiles[$setCount] = $fileLocation;

        $dir = $tempFolder . "/";

        $zip = new ZipArchive;
        $res = $zip->open($fileLocation, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($res === TRUE) {
            foreach (glob($dir . '*') as $file) {
                $zip->addFile($file, basename($file));
            }

            $demoFilePath = './Download/dummy.txt';
            $zip->addFile($demoFilePath, basename($demoFilePath));
            
            $zip->close();
        } else {
            echo 'Failed to create to zip. Error: ' . $res;
            die();
        }

        $files = glob($tempFolder . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }

        $setCount++;
        $count = 0;
    }
}


$files = glob($tempFolder . '/*'); // get all file names
foreach ($files as $file) { // iterate files
    if (is_file($file)) {
        unlink($file); // delete file
    }
}
rmdir($tempFolder);


foreach ($temfiles as $tmpFile) {

?>
    <script>
        window.open('<?php echo $tmpFile ?>')
    </script>
<?php
}

?>

<script>
    window.close();
</script>
