<?php
/**
 * Created by PhpStorm.
 * User: ivanatora
 * Date: 06.10.18
 * Time: 20:03
 */
echo 'bau de';
$num_input = 2;
$num_output = 1;
$num_layers = 3;
$num_neurons_hidden = 3;
$desired_error = 0.001;
$max_epochs = 500000;
$epochs_between_reports = 1000;
print "predi";
try {
    $ann = fann_create_standard($num_layers, $num_input, $num_neurons_hidden, $num_output);
    var_dump($ann);
} catch (Exception $e) {
    print $e->getMessage();
}
var_dump($ann);
print "ei go";
if ($ann) {
    fann_set_activation_function_hidden($ann, FANN_SIGMOID_SYMMETRIC);
    fann_set_activation_function_output($ann, FANN_SIGMOID_SYMMETRIC);
    $filename = dirname(__FILE__) . "/../storage/xor.data";
    if (fann_train_on_file($ann, $filename, $max_epochs, $epochs_between_reports, $desired_error))
        print('xor trained.<br>' . PHP_EOL);
    if (fann_save($ann, dirname(__FILE__) . "/../storage/xor_float.net"))
        print('xor_float.net saved.<br><a href="simple_test.php">Test</a>' . PHP_EOL);
    fann_destroy($ann);
} else {
    print "no fann";
}