<?php namespace Clockwork\Support\Twig;

use Clockwork\Request\Timeline\Timeline;

// Converts Twig profiles to a Clockwork rendered views timelines
class ProfilerClockworkDumper
{
	protected $lastId = 1;

	// Dumps a profile into a new rendered views timeline
	public function dump($profile)
	{
		$timeline = new Timeline;

		$this->dumpProfile($profile, $timeline);

		return $timeline;
	}

	public function dumpProfile($profile, Timeline $timeline, $parent = null)
	{
		$id = $this->lastId++;

		if ($profile->isRoot()) {
			$name = $profile->getName();
		} elseif ($profile->isTemplate()) {
			$name = basename($profile->getTemplate());
		} else {
			$name = basename($profile->getTemplate()) . '::' . $profile->getType() . '(' . $profile->getName() . ')';
		}

		foreach ($profile as $p) {
			$this->dumpProfile($p, $timeline, $id);
		}

		$data = $profile->__serialize();

		$timeline->event($name, [
			'name'  => $id,
			'start' => $data[3]['wt'] ?? null,
			'end'   => $data[4]['wt'] ?? null,
			'data'  => [
				'data'        => [],
				'memoryUsage' => $data[4]['mu'] ?? null,
				'parent'      => $parent
			]
		]);
	}
}
