<?php
class notify_hook extends Hook
{
	/* 控制器方法之前 */
	public function html_load() {
		$queue = new queue();
		$queue->run();
	}
}