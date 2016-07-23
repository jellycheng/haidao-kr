<?php
class admin_hook
{
	public function update_cache() {
		model('admin/plugin','service')->build_cache();
		model('admin/module','service')->build_cache();
	}
}