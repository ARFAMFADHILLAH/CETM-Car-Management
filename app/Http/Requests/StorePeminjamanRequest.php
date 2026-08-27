<?php

namespace App\Http\Requests;

use App\Enums\PeminjamanStatus;
use App\Models\Peminjaman;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'email_peminjam' => ['required', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'divisi_id' => ['nullable', 'integer', 'exists:divisi,id'],
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keperluan' => ['required', 'string', 'max:255'],
            'lokasi_tujuan' => ['required', 'string', 'max:255'],
            'tujuan' => ['required', 'string', 'in:dalam_kota,luar_kota'],
            'km_awal' => ['required', 'integer', 'min:0'],
            'km_akhir' => ['nullable', 'integer', 'min:0'],
            'tangki_bbm' => ['required', 'string', 'in:full,3/4,1/2,1/4,empty'],
            'nama_customer' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance to check for booking conflicts.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $carId = $this->input('car_id');
            $tanggalMulai = $this->input('tanggal_mulai');
            $tanggalSelesai = $this->input('tanggal_selesai');

            $activeStatuses = [
                PeminjamanStatus::Pending->value,
                PeminjamanStatus::Disetujui->value,
            ];

            $hasConflict = Peminjaman::query()
                ->where('car_id', $carId)
                ->whereIn('status', $activeStatuses)
                ->where('tanggal_mulai', '<', $tanggalSelesai)
                ->where('tanggal_selesai', '>', $tanggalMulai)
                ->exists();

            if ($hasConflict) {
                $validator->errors()->add(
                    'car_id',
                    'Mobil ini sudah dibooking untuk tanggal yang dipilih. Silakan pilih tanggal atau mobil lain.'
                );
            }
        });
    }
}
