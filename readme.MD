example usage: php example.php

For those who don't have PHP installed:

docker build -t sorted-linked-list . && docker run --rm sorted-linked-list // This will run example.php in the root directory.

Note for reviewers: The commit titled “refactor: clean up …” only introduces a few optimizations. For a demo implementation it would likely work fine without it.