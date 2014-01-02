<?php

namespace EasyDB;

interface ConnectionInteface
{
	public function create();

	public function execute();

	public function destroy();
}