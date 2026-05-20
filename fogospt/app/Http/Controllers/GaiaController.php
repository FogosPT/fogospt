<?php

namespace App\Http\Controllers;

use App\Libs\GaiaApi;
use Illuminate\Http\Request;

class GaiaController extends Controller
{
    protected $pageName;

    public function getPageName()
    {
        return $this->pageName;
    }

    private const PT_BBOX = [
        'NorthEastBoundary.Latitude'  => 42.2,
        'NorthEastBoundary.Longitude' => -6.1,
        'SouthWestBoundary.Latitude'  => 36.9,
        'SouthWestBoundary.Longitude' => -9.6,
    ];

    public function getIndex()
    {
        $this->pageName = 'Início (Gaia preview)';
        return view('index')->with([
            'metadata' => $this->generateMetadata(),
            'gaia'     => true,
        ]);
    }

    private function bboxFromRequest(Request $request)
    {
        $keys = array_keys(self::PT_BBOX);
        $hasAny = false;
        foreach ($keys as $k) {
            if ($request->filled($k)) {
                $hasAny = true;
                break;
            }
        }

        if (!$hasAny) {
            return self::PT_BBOX;
        }

        $out = [];
        foreach ($keys as $k) {
            if ($request->filled($k)) {
                $out[$k] = (float) $request->query($k);
            }
        }
        return $out;
    }

    private function commonFilters(Request $request, array $allowed)
    {
        $out = [];
        foreach ($allowed as $k) {
            if ($request->filled($k)) {
                $out[$k] = $request->query($k);
            }
        }
        return $out;
    }

    public function events(Request $request)
    {
        $filters = array_merge(
            $this->bboxFromRequest($request),
            $this->commonFilters($request, ['StartDate', 'EndDate', 'is_active', 'sources', 'limit'])
        );

        $data = GaiaApi::getEvents($filters);
        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function eventDetail($id)
    {
        $data = GaiaApi::getEvent($id);
        if (isset($data['error'])) {
            return response()->json($data, 502)->header('Cache-Control', 'no-store');
        }
        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function timeline($id, Request $request)
    {
        $filters = $this->commonFilters($request, ['StartDate', 'EndDate', 'limit']);
        $data = GaiaApi::getEventTimeline($id, $filters);
        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function acquisitions($id, Request $request)
    {
        $filters = $this->commonFilters($request, ['StartDate', 'EndDate', 'relationship_type', 'invalid', 'limit']);
        $data = GaiaApi::getEventAcquisitions($id, $filters);
        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=1800');
    }

    public function detections(Request $request)
    {
        $filters = array_merge(
            $this->bboxFromRequest($request),
            $this->commonFilters($request, ['event_id', 'StartDate', 'EndDate', 'sources', 'min_probability', 'limit'])
        );
        $data = GaiaApi::getDetections($filters);
        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function delineations(Request $request)
    {
        $filters = $this->commonFilters($request, ['event_id', 'StartDate', 'EndDate', 'limit']);
        $data = GaiaApi::getDelineations($filters);
        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=1800');
    }
}
