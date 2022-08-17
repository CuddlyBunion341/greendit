<?php require __DIR__ . '/require/header.php'; ?>
<?php
require 'require/util.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /greendit/login.php');
    exit();
}
$values = array(
    'name' => '',
    'display' => '',
    'radio1' => true,
    'radio2' => false,
);
$errors = array();
function val($name) {
    if (isset($_POST[$name])) {
        return trim(htmlspecialchars($_POST[$name]));
    }
    return null;
}
function checked($name) {
    global $values;
    if ($values[$name] == true) {
        return 'checked';
    }
    return '';
}
function error($name) {
    global $errors;
    if (isset($errors[$name])) {
        return '<p class="error">'.$errors[$name].'</p>';
    }
    return '';
}
if (isset($_POST['submit'])) {
    $name = val('name');
    $display = val('display');
    $type = val('type');

    $values['name'] = $name;
    $values['display'] = $display;

    if ($type == 'public') {
        $values['radio1'] = true;
        $values['radio2'] = false;
    } else {
        $values['radio1'] = false;
        $values['radio2'] = true;
    }

    if (!preg_match('/\w{3,24}/',$name)) {
        $errors['name'] = 'name must be between 3 and 24 characters';
    }
    if (!preg_match('/.{3,45}/',$display)) {
        $errors['display'] = 'name must be between 3 and 45 characters';
    }

    if (empty($errors)) {
        require_once 'require/db_connect.php';
        $user_id = $_SESSION['user_id'];
        if (execute("insert into communities (shortname, name, user_id) values ('$name', '$display', $user_id)")) {
            header('Location: /greendit/subs/' . $name);
            exit('Success!');
        }
    }
}
?>
<main>
<article class="community-composer">
    <form action="create.php" method="post">
        <h1>Create a community</h1>
        <div class="field-group">
            <label for="name">Community name</label><br>
            <div class="fancy-text-input">
                <span class="fancy-text-input__prepend">s/</span>
                <input class="fancy-text-input__input" type="text" name="name" id="name" maxlength="24" value="<?= $values['name'] ?>">
            </div>
            <?= error('name'); ?>
        </div>
        <div class="field-group">
            <label for="display">Display name</label><br>
            <input type="text" name="display" id="display" maxlength="45" value="<?= $values['display'] ?>">
            <?= error('display'); ?>
        </div>
        <div class="field-group">
            <p>Community type</p>
            <input type="radio" name="type" id="radio-1" value="public" <?= checked('radio1') ?>>
            <label for="radio-1"><?= icon('user','radio-icon'); ?>Public</label><br>
            <input type="radio" name="type" id="radio-2" value="private" <?= checked('radio2') ?>>
            <label for="radio-2"><?= icon('lock','radio-icon'); ?>Private</label><br>
        </div>
        <input type="submit" name="submit" value="Create">
    </form>
</article>
</main>