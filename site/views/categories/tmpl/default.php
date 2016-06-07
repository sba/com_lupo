<?php
/**
 * @package		Joomla
 * @subpackage	LUPO
 * @copyright   Copyright (C) databauer / Stefan Bauer 
 * @author		Stefan Bauer
 * @link		http://www.ludothekprogramm.ch
 * @license		License GNU General Public License version 2 or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//load lupo styles
JHTML::stylesheet('com_lupo.css', 'components/com_lupo/css/');
$componentParams = JComponentHelper::getParams('com_lupo');
$menu = JSite::getMenu()->getActive();
?>

<article class="tm-article">
	<div class="tm-article-content ">

		<?php if ($menu->params->get('show_page_heading', 1)) : ?>
			<h2 class="contentheading"><?php echo $menu->params->get('page_heading')?></h2>
		<?php endif; ?>

		<ul class="lupo lupo_categories">
			<?php
			foreach($this->categories as $category){?>
				<li><a href="<?php echo $category['link']?>"><?php echo $category['title']?></a>
				<?php if($componentParams->get('cats_nbr_games', '1')) { ?>
					(<?php echo $category['number']?>)
				<?php } ?>
				</li>
			<?php } ?>
		</ul>

	</div>
</article>