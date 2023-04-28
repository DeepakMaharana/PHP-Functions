function sanitize($value)
{
    global $mysqli;

    if(is_array($value))
    {
        $value = array_map('sanitize', $value);
        return $value;
    }
    else
    {
        return mysqli_real_escape_string($mysqli, $value);
    }
    // return $mysqli->real_escape_string($value);
}

    if($_POST){$_POST = array_map('sanitize', $_POST);}
    extract($_POST);
