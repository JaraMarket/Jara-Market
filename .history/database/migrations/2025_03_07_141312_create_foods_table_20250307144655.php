 $table->id();
            $table->string('name');
            $table->text('description');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();