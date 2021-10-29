<?PHP

namespace App;

use App\Exceptions\ValidationException;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Handlers\IExceptionHandler;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

class ErrorHandler implements IExceptionHandler
{
	public function handleError(Request $request, \Exception $error): void
	{
        if ($error instanceof ValidationException)
        {
            response()->httpCode($error->getCode())->json([
                'message' => $error->getMessage(),
                'code'  => $error->getCode(),
                'errors' => $error->getErrors(),
            ]);
        }

        response()->httpCode(500)->json([
            'message' => $error->getMessage(),
            'code'  => $error->getCode(),
        ]);


	}

}