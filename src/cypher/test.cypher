match (n:NodeLable {
  string: $string,
  int:$int,
  float: $float,
  float2: $float2,
  true: $true,
  false: $false
})
return n limit 2;
