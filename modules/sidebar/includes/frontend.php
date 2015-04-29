<?php

if(!empty($settings->sidebar)) {
	if(!dynamic_sidebar($settings->sidebar)) {
		dynamic_sidebar();
	}
}