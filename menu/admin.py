from django.contrib import admin
from .models import Categoria, Plato

@admin.register(Plato)
class PlatoAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'categoria', 'precio', 'disponible')
    list_filter = ('categoria', 'disponible')
    search_fields = ('nombre',)

admin.site.register(Categoria)
