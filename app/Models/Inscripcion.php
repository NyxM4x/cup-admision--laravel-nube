<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Inscripcion extends Model
{
    protected $table = 'inscripciones';
    protected $fillable = [
        'postulante_id',
        'periodo_id',
        'fecha_inscripcion',
        'estado',
    ];
    protected $casts = [
        'fecha_inscripcion' => 'date',
    ];
    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
    public function postulacionCarreras()
    {
        return $this->hasMany(PostulacionCarrera::class)->orderBy('prioridad');
    }
    public function documentos()
    {
        return $this->hasMany(DocumentoPostulante::class);
    }
    public function pago()
    {
        return $this->hasOne(Pago::class)->latest();
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
    public function tienePagoPendiente(): bool
    {
        return $this->pagos()->where('estado', 'pendiente')->exists();
    }
}