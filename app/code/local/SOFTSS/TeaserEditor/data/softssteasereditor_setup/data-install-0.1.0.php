<?php

$installer = $this;

/**
 * Insert teaser groups
 */
$aTeaserGroupData = array(
    array(
        'teaser_group_id'   => 1,
        'teaser_group_name' => 'Homepage Left Sidebar',
    ),
    array(
        'teaser_group_id'   => 2,
        'teaser_group_name' => 'Homepage Bottom',
    ),
    array(
        'teaser_group_id'   => 3,
        'teaser_group_name' => 'Homepage Right Sidebar',
    ),
    array(
        'teaser_group_id'   => 4,
        'teaser_group_name' => 'Homepage Slider',
    ),
    array(
        'teaser_group_id'   => 5,
        'teaser_group_name' => 'Category',
    )
);
$installer->getConnection()->insertMultiple($installer->getTable('softssteasereditor/teaser_group'), $aTeaserGroupData);


/**
 * Insert teaser
 */
$aTeaserData = array(
    array(
        'id'              => 1,
        'teaser_group_id' => 1,
        'title' => 'dummy_01',
        'image' => 'dummy_01.png',
        'link' => 'http://www.example.com',
        'sort' => '1'
    ),
    array(
        'id'              => 2,
        'teaser_group_id' => 1,
        'title' => 'dummy_02',
        'image' => 'dummy_02.png',
        'link' => 'http://www.example.com',
        'sort' => '2'
    ),
    array(
        'id'              => 3,
        'teaser_group_id' => 1,
        'title' => 'dummy_03',
        'image' => 'dummy_03.jpg',
        'link' => 'http://www.example.com',
        'sort' => '3'
    ),
    array(
        'id'              => 4,
        'teaser_group_id' => 2,
        'title' => 'dummy_04',
        'image' => 'dummy_04.jpg',
        'link' => 'http://www.example.com',
        'sort' => '4'
    ),
    array(
        'id'              => 5,
        'teaser_group_id' => 2,
        'title' => 'dummy_05',
        'image' => 'dummy_05.jpg',
        'link' => 'http://www.example.com',
        'sort' => '5'
    ),
    array(
        'id'              => 6,
        'teaser_group_id' => 2,
        'title' => 'dummy_06',
        'image' => 'dummy_06.jpg',
        'link' => 'http://www.example.com',
        'sort' => '6'
    ),
    array(
        'id'              => 7,
        'teaser_group_id' => 2,
        'title' => 'dummy_07',
        'image' => 'dummy_07.jpg',
        'link' => 'http://www.example.com',
        'sort' => '7'
    ),
    array(
        'id'              => 8,
        'teaser_group_id' => 2,
        'title' => 'dummy_08',
        'image' => 'dummy_08.jpg',
        'link' => 'http://www.example.com',
        'sort' => '8'
    ),
    array(
        'id'              => 9,
        'teaser_group_id' => 3,
        'title' => 'dummy_09',
        'image' => 'dummy_09.jpg',
        'link' => 'http://www.example.com',
        'sort' => '9'
    ),
    array(
        'id'              => 10,
        'teaser_group_id' => 3,
        'title' => 'dummy_10',
        'image' => 'dummy_10.jpg',
        'link' => 'http://www.example.com',
        'sort' => '10'
    ),
    array(
        'id'              => 11,
        'teaser_group_id' => 3,
        'image' => 'dummy_11.jpg',
        'link' => 'http://www.example.com',
        'sort' => '11'
    ),
    array(
        'id'              => 12,
        'teaser_group_id' => 4,
        'title' => 'dummy_12',
        'image' => 'dummy_12.jpg',
        'link' => 'http://www.example.com',
        'sort' => '12'
    ),
    array(
        'id'              => 13,
        'teaser_group_id' => 4,
        'title' => 'dummy_13',
        'image' => 'dummy_13.jpg',
        'link' => 'http://www.example.com',
        'sort' => '13'
    ),
    array(
        'id'              => 14,
        'teaser_group_id' => 4,
        'title' => 'dummy_14',
        'image' => 'dummy_14.jpg',
        'link' => 'http://www.example.com',
        'sort' => '14'
    )
);

foreach ($aTeaserData as $aBind) {
    $installer->getConnection()->insertForce($installer->getTable('softssteasereditor/teaser'), $aBind);
}
