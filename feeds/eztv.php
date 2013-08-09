<?php
class EZTV implements Feed{

	const PATH = "http://eztv.it/";
	
    public static function getFeedStats(){
        return "n/a";
    }

    public static function getFeedUsageInPercentage(){
        return -1;
    }

    public static function getFeedTimeRemaining(){
        return "n/a";
    }

    public static function getWebsiteLinkToShow( $show_id ){
        return self::PATH . 'shows/' . $show_id;
    }

	// For this feed, this function returns a cURL handle
    public static function getShowFeed( $show ){
        if( isset($show) ){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, self::PATH . 'shows/' . $show);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			return $ch;
        }
    }

    public static function launchDownloads( $preview = false ){
        $added = array();
		$could_be_added = array();

		// Retrieve content of pages
		$pages = self::doCurlMulti();

		// Parse pages and retrieve links that could be added
		foreach( $pages as $page ){
			self::parsePage($page, $could_be_added);
		}

		foreach( $could_be_added as $ep ){
			$tmp = Utils::downloadTorrent($ep, $preview);
			if( isset($tmp) ) $added[] = $tmp;
		}

        return $added;
    }

	// Create cURL handles, add them to a multi handle, and then run them in parallel
	// Based on the example of http://php.net/manual/en/function.curl-multi-exec.php
	private static function doCurlMulti(){

		// Create a new cURL multi handle
		$mh = curl_multi_init();

        // Get and add handles
		foreach( Utils::getShowList() as $show ){
            if( !empty($show) ){
                $ch = self::getShowFeed($show);
				curl_multi_add_handle($mh, $ch);
				$handles[] = $ch;
            }
        }

		// Process each of the handles in the stack
		$active = null;
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {
			if (curl_multi_select($mh) != -1) {
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}

		// Retrieve the content of cURL handles and remove them
		foreach( $handles as $ch ){
			$pages[] = curl_multi_getcontent($ch);
			curl_multi_remove_handle($mh, $ch);
		}

		// Close the cURL multi handle
		curl_multi_close($mh);

		return $pages;
	}

	// This parsing is crappy but the DOM is crappy too :p
	private static function parsePage( $page, &$could_be_added ){

		$dom = new DomDocument();
		// Warnings are muted because DOM retrieved is not valid
        @$dom->loadHTML($page);
		$finderEpinfo = new DomXPath($dom);

		// Find lines corresponding to an episode
		$nodes = $finderEpinfo->query("//*[contains(@class, 'epinfo')]");

		foreach( $nodes as $item ){
			// Date of realesed
			$realeased = $item->parentNode->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;

			// At the moment, for EZTV we only handle episodes with a realease date below a week
			if( $realeased != ">1 week" ){

				$episodeName = $item->nodeValue;
				$infoLink = $item->parentNode->previousSibling->previousSibling->firstChild->nextSibling->getAttribute('href');
				$downloadLink = $item->parentNode->nextSibling->nextSibling->firstChild->getAttribute('href');

				if( !array_key_exists($infoLink, $could_be_added) ){
					$could_be_added[$infoLink] = $downloadLink;
				}else{
					// If current download link contains preferred format replace link previously set in array
					if( strpos($downloadLink, PREFERRED_FORMAT) !== false  ){
						$could_be_added[$infoLink] = $downloadLink;
					}
				}
			}
		}
	}

}

?>