# Cabinetry

## Patches needed
This module currently needs core patch:
https://www.drupal.org/files/issues/views_does_not_add_relationship_2795455_13.patch
Or the views will error. This may not be the case once issue #2795455 is merged.

## Operation Assumptions
Choosing axes in space when computing the breakdown of sheet goods and cabinet design is challenging. To introduce sanity, the following standards have been adhered to:

### Coordinate axes chosen for cabinets.
* The axis parallel to the ground is always X (Length).
* The axis pointing to the ceiling is always Y (Width).
* The axis perpindicular to both X and Y is Z (Depth).

### Coordinate axes chosen for wood pieces.
* If a piece has wood grain (or veneer), the direction parallel to the grain is always considered X (length).
* If no grain is present, the longest side of the parent (or sheet) piece is considered X.
* The axis that is perpinducular to X, and across the face of the wood piece is then Y (width).
* The axis perpindicular to both X and Y is then Z (depth).
