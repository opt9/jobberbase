<?php
	if ($id != '' && !strstr($id, '|'))
	{
		$keywords = $db->real_escape_string(trim($id));
	}
	else
	{
		if (!empty($_POST['keywords']) && $_POST['keywords'] != '' && $_POST['keywords'] != 'caută un job')
		{
			$keywords = $db->real_escape_string(trim($_POST['keywords']));
		}
		else if (strstr($id, '|'))
		{
			$tmp = explode('|', $id);
			$categ = trim($tmp[0]);
			$keywords = trim($tmp[1]);
			$keywords = urldecode($keywords);
			// clicked on a city on the map
			if (isset($tmp[2]) && $tmp[2] == 'map')
			{
				$city = get_city_id_by_asciiname($keywords);
				$keywords = $city['name'];
			}
		}
		else
		{
			redirect_to(BASE_URL);
			exit;
		}
	}
	
	// record search keywords
	$_SESSION['search_keywords'] = $keywords;
	
	$is_home = false;

	if ($keywords == '' || $keywords == ' ')
	{
		if ($categ == '')
		{
			$smarty->assign('no_categ', 1);
		}
		else if ($categ == 'home')
		{
			$is_home = true;
			$smarty->assign('is_home', 1);
		}
		$smarty->assign('jobs', $job->GetJobs(0, $categ, 0, 0, 0));
	}
	else
	{
		$smarty->assign('jobs', $job->Search($keywords));
	}
	// if user hit enter after entering a search query, we know this causes a 
	// synchronous HTTP redirect, so apply a different template
	if ($is_home)
	{
		$template = 'home.tpl';
	}
	if (!empty($_POST['keywords']))
	{
		// save recorded keywords, if available
		if ($_SESSION['search_keywords'])
		{
			$search = new SearchKeywords($_SESSION['search_keywords']);
			$search->Save();
			unset($_SESSION['search_keywords']);
		}
		$smarty->assign('keywords', stripslashes(htmlentities($_POST['keywords'], ENT_QUOTES)));
		$template = 'search.tpl';
	}
	else if ($id != '' && !strstr($id, '|'))
	{
		$smarty->assign('keywords', stripslashes(htmlentities($id, ENT_QUOTES)));
		$template = 'search.tpl';
	}
	else
	{
		$smarty->assign('keywords', stripslashes($keywords));
		$template = 'posts-loop.tpl';
	}
?>