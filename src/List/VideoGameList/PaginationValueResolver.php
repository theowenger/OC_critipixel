<?php

declare(strict_types=1);

namespace App\List\VideoGameList;

use App\Model\ValueObject\Direction;
use App\Model\ValueObject\Sorting;
use ArrayIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Traversable;

#[AsTargetedValueResolver('pagination')]
final readonly class PaginationValueResolver implements ValueResolverInterface
{
    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return ArrayIterator<int, Pagination>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Traversable
    {
        $argumentType = $argument->getType();

        if ($argumentType !== Pagination::class) {
            return new ArrayIterator();
        }

        // Retourne un ArrayIterator avec l'objet Pagination
        return new ArrayIterator([new Pagination(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10),
            Sorting::tryFromName($request->query->get('sorting', '')) ?? Sorting::ReleaseDate,
            Direction::tryFromName($request->query->get('direction', '')) ?? Direction::Descending,
        )]);
    }
}
