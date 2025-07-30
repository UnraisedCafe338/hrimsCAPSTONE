<?php

if (class_exists('MongoDB\BSON\ObjectId')) {
    echo "MongoDB ObjectId class is available!";
} else {
    echo "MongoDB ObjectId class is NOT available.";
}
