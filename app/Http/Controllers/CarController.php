<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $cars = Car::query()
            ->orderBy('nama')
            ->get();

        return Inertia::render('mobil/Index', [
            'cars' => $cars,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('cars', 'public');
        }

        Car::create($data);

        return to_route('mobil.index')
            ->with('success', 'Mobil berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($car->foto) {
                Storage::disk('public')->delete($car->foto);
            }

            $data['foto'] = $request->file('foto')->store('cars', 'public');
        }

        $car->update($data);

        return to_route('mobil.index')
            ->with('success', 'Mobil berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car): RedirectResponse
    {
        if ($car->foto) {
            Storage::disk('public')->delete($car->foto);
        }

        $car->delete();

        return to_route('mobil.index')
            ->with('success', 'Mobil berhasil dihapus.');
    }
}
