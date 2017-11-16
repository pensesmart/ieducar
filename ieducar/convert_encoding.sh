$local = "ieducar/ieducar/"

for arquivo in $(find $local -name '*.php'); do
	retorno=$(file -bi $arquivo)
	if [ "$retorno" = "text/x-php; charset=iso-8859-1" ]; then
		echo "encoding: $retorno --- $arquivo" >> iso88.txt
		#echo "$local/copia/${arquivo#local}"
		iconv -f iso-8859-1 -t utf-8 "$arquivo" -o "$local/copia/${arquivo#local}"
		echo true
	else
		echo "encoding: $retorno --- $arquivo" >> utf8.txt
		echo false
	fi
done
