<?php
include_once __DIR__.'/FlickrApi.php';

function getPhotos($tags) {
    $flickr = new FlickrApi('PUT YOU API KEY HERE');

    $params = array(
        'method'	=> 'flickr.photos.search',

        'tags' => $tags,
        'license' => '4,5,6,7',
        'content_type' => 1, //photos only
        'extras' => 'url_l,url_sq',
        'media' => 'photos',
        'per_page' => 100,
        'sort' => 'relevance',

    );
    $photos = $flickr->api($params); // get photos

    $result = array();
    foreach($photos['photos']['photo'] as $p) {
        if(!isset($p['url_l'])) continue; // doesn't have big image, skip it

        $params = array(
            'method'	=> 'flickr.photos.getFavorites',
            'photo_id' => $p['id'],
        );
        $favs = $flickr->api($params);
        $favs = $favs['photo']['total']; // total count of favorites

        if($favs >= 5) {
            $params = array(
                'method'	=> 'flickr.photos.getInfo',
                'photo_id' => $p['id'],
            );

            $photo = $flickr->api($params);
            $data = array();
            $data['url_l'] = $p['url_l'];
            $data['url_sq'] = $p['url_sq'];
            $data['favorites'] = $favs;
            $data['owner'] = $photo['photo']['owner']['username'];
            $data['title'] = $photo['photo']['title']['_content'];
            $data['description'] = $photo['photo']['description']['_content'];
            $data['url'] = $photo['photo']['urls']['url'][0]['_content'];

            $result[] = $data;
        }
        if(count($result) >= 10) return $result;

    }
    return $result;
}

$tags = isset($_GET['tag']) ? $_GET['tag'] : 'landscape';
$res = getPhotos($tags);

?>

<table width="80%">
    <tr>
        <td>Image</td>
        <td>Title</td>
        <td>Owner</td>
        <td width="50%">Description</td>
        <td>Favorites</td>
        <td>Flickr page</td>

    </tr>
<?php

foreach($res as $r) {
    ?>
    <tr>
        <td><a href="<?php echo $r['url_l']?>"><img src="<?php echo $r['url_sq'] ?>" /></td>
        <td><?php echo $r['title']?></td>
        <td><?php echo $r['owner']?></td>
        <td><?php echo $r['description']?></td>
        <td><?php echo $r['favorites']?></td>
        <td><a href="<?php echo $r['url']?>">see on flickr</td>
    </tr>

    <?php
}

?>
</table>






