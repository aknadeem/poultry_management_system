<?php

namespace App\Http\Controllers\Inertia\FarmManagement;

use App\Actions\FarmManagement\RecordVaccinationAction;
use App\Actions\FarmManagement\StoreVaccinationScheduleAction;
use App\Actions\PartyManagement\UpdateActiveStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FarmManagement\RecordVaccinationRequest;
use App\Http\Requests\FarmManagement\StoreVaccinationScheduleRequest;
use App\Models\VaccinationSchedule;
use App\Queries\VaccinationScheduleQuery;
use App\Support\FarmLookups;
use App\Support\FarmPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VaccinationController extends Controller
{
    public function index(VaccinationScheduleQuery $query): Response
    {
        $schedules = $query->paginate();

        return Inertia::render('Vaccinations/Index', array_merge(FarmLookups::formOptions(), [
            'schedules' => $schedules->through(
                fn (VaccinationSchedule $schedule): array => FarmPresenter::vaccinationSchedule($schedule)
            ),
            'filters' => $query->filters(),
        ]));
    }

    public function store(StoreVaccinationScheduleRequest $request, StoreVaccinationScheduleAction $action): RedirectResponse
    {
        $action->execute($request->validated(), (int) Auth::id());

        return redirect()
            ->route('inertia.vaccinations.index')
            ->with('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Vaccination schedule added successfully!',
            ]);
    }

    public function record(
        RecordVaccinationRequest $request,
        RecordVaccinationAction $action,
    ): RedirectResponse {
        $schedule = VaccinationSchedule::query()->findOrFail($request->integer('schedule_id'));
        $action->execute($schedule, $request->validated(), (int) Auth::id());

        return redirect()
            ->route('inertia.vaccinations.index')
            ->with('swal_notification', [
                'title' => 'Success',
                'icon_type' => 'success',
                'message' => 'Vaccine has been  added successfully!',
            ]);
    }

    public function toggleStatus(
        VaccinationSchedule $vaccination,
        UpdateActiveStatusAction $action,
    ): RedirectResponse {
        $action->execute($vaccination->id, 'vaccination_schedules', (int) Auth::id());

        return redirect()
            ->route('inertia.vaccinations.index')
            ->with('swal_notification', [
                'title' => 'Updated',
                'icon_type' => 'success',
                'message' => 'Status updated successfully',
            ]);
    }
}
