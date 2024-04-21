<?php

class Card {
	private int $post_id;
	private bool $with_excerpt;
	private bool $with_image;
	private bool $with_button;
	private string $orientation;
	private array $extra_links;
	private string $read_more_text;

	public function __construct(int $post_id, bool $with_excerpt = true, bool $with_image = true, bool $with_button = true, string $orientation = 'horizontal', string $read_more_text = 'Read more', array $extra_links = []) {
		$this->post_id = $post_id;
		$this->with_excerpt = $with_excerpt;
		$this->with_image = $with_image;
		$this->with_button = $with_button;
		$this->orientation = $orientation;
		$this->extra_links = $extra_links;
		$this->read_more_text = $read_more_text;

		return $this;
	}

	public function getPostId(): int {
		return $this->post_id;
	}

	public function isWithExcerpt(): bool {
		return $this->with_excerpt;
	}

	public function isWithImage(): bool {
		return $this->with_image;
	}

	public function isWithButton(): bool {
		return $this->with_button;
	}

	public function getOrientation(): string {
		return $this->orientation;
	}

	public function getExtraLinks(): array {
		return $this->extra_links;
	}

	public function getReadMoreText(): string {
		return $this->read_more_text;
	}
}
